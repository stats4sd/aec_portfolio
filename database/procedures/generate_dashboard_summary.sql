DROP PROCEDURE IF EXISTS `generate_dashboard_summary`;

CREATE PROCEDURE `generate_dashboard_summary`(
    IN dashboardYoursId INT,
    IN dashboardOthersId INT,
    IN organisationId INT,
    IN portfolioId INT,
    IN regionIds VARCHAR(255),
    IN countryIds VARCHAR(255),
    IN categoryIds VARCHAR(255),
    IN projectStartFrom INT,
    IN projectStartTo INT,
    IN budgetFrom INT,
    IN budgetTo INT,
    OUT status INT,
    OUT message VARCHAR(1000),
    OUT totalCount BIGINT,
    OUT totalBudget BIGINT,
    OUT tooFewOtherProjects INT,
    OUT statusSummary VARCHAR(16383),
    OUT redlinesSummary VARCHAR(16383),
    OUT yoursPrinciplesSummary VARCHAR(16383),
    OUT othersPrinciplesSummary VARCHAR(16383)
)
BEGIN

    -- variables for status summary
    DECLARE ssCreatedCount INT;
    DECLARE ssCreatedPercent DECIMAL(3);
    DECLARE ssCreatedBudget BIGINT;
    DECLARE ssPassedAllCount INT;
    DECLARE ssPassedAllPercent DECIMAL(3);
    DECLARE ssPassedAllBudget BIGINT;
    DECLARE ssFailedAnyCount INT;
    DECLARE ssFailedAnyPercent DECIMAL(3);
    DECLARE ssFailedAnyBudget BIGINT;
    DECLARE ssFullyAssessedCount INT;
    DECLARE ssFullyAssessedPercent DECIMAL(3);
    DECLARE ssFullyAssessedBudget BIGINT;

    -- variables for red line summary
    DECLARE rsRedLineCount INT;
    DECLARE rsRedLineId INT;
    DECLARE rsRedLineName VARCHAR(16383);
    DECLARE rsYourPercentage FLOAT;
    DECLARE rsOthersPercentage FLOAT;
    DECLARE rsYourFinalPercentage FLOAT;
    DECLARE rsOthersFinalPercentage FLOAT;

    -- variables for principles summary
    DECLARE psPrincipleCount INT;


    -- variables for anonymity check
    DECLARE ssOtherProjectCount INT;
    DECLARE ssOtherOrganisationCount INT;


    -- variable to determine whether it is end of cursor
    DECLARE rsDone INT DEFAULT FALSE;

    -- cursor to join yours, others, redlines as redlines summary
    DECLARE rsCursor CURSOR FOR
        SELECT ta.red_line_id, tc.name, ta.percentage AS yours_percentage, tb.percentage AS others_percentage
        FROM (SELECT * FROM dashboard_red_line WHERE dashboard_id = dashboardYoursId) AS ta,
             (SELECT * FROM dashboard_red_line WHERE dashboard_id = dashboardOthersId) AS tb,
             red_lines tc
        WHERE ta.red_line_id = tb.red_line_id
          AND ta.red_line_id = tc.id;

    -- handler declaration
    DECLARE CONTINUE HANDLER FOR NOT FOUND SET rsDone = TRUE;


    -- remove existing records if any
    DELETE FROM dashboard_assessment WHERE dashboard_id IN (dashboardYoursId, dashboardOthersId);
    DELETE FROM dashboard_principle WHERE dashboard_id IN (dashboardYoursId, dashboardOthersId);
    DELETE FROM dashboard_project WHERE dashboard_id IN (dashboardYoursId, dashboardOthersId);
    DELETE FROM dashboard_red_line WHERE dashboard_id IN (dashboardYoursId, dashboardOthersId);
    DELETE FROM dashboard_result WHERE dashboard_id IN (dashboardYoursId, dashboardOthersId);


    -- initialise status and message
    SET status = 0;
    SET message = 'Success';


    -- debug message
    -- SELECT dashboardYoursId, dashboardOthersId, organisationId, portfolioId, regionId, countryId, projectStartFrom, projectStartTo, budgetFrom, budgetTo;


    -- -------------------------
    -- FIND PROJECTS AND LATEST ASSESSMENTS (YOURS)
    -- -------------------------


    -- find all project ids that fulfill criteria (yours)

    /*

    Example SQL:

    SELECT p.*, pr.region_id, cp.country_id
    FROM projects p
    LEFT JOIN project_region pr
    ON p.id = pr.project_id
    LEFT JOIN country_project cp
    ON p.id = cp.project_id
    WHERE p.id = p.id
    AND p.organisation_id = 9
    AND p.portfolio_id = 20
    AND YEAR(p.start_date) BETWEEN 2020 AND 2030'
    AND p.budget BETWEEN 200 AND 1000
    AND pr.region_id = 11
    AND cp.country_id = 132;

    */

    -- construct dynamic SQL
    SET @SQLText = '';
    SET @SQLText = CONCAT(@SQLText, ' INSERT INTO dashboard_project (dashboard_id, project_id)');
    SET @SQLText = CONCAT(@SQLText, ' SELECT ', dashboardYoursId, ', p.id');
    SET @SQLText = CONCAT(@SQLText, ' FROM projects p');
    SET @SQLText = CONCAT(@SQLText, ' LEFT JOIN project_region pr');
    SET @SQLText = CONCAT(@SQLText, ' ON p.id = pr.project_id');
    SET @SQLText = CONCAT(@SQLText, ' LEFT JOIN country_project cp');
    SET @SQLText = CONCAT(@SQLText, ' ON p.id = cp.project_id');
    SET @SQLText = CONCAT(@SQLText, ' WHERE p.deleted_at IS NULL');


    -- criteria
    IF organisationId IS NOT NULL THEN
        SET @SQLText = CONCAT(@SQLText, ' AND p.organisation_id = ', organisationId);
    END IF;

    IF portfolioId IS NOT NULL THEN
        SET @SQLText = CONCAT(@SQLText, ' AND p.portfolio_id = ', portfolioId);
    END IF;

    IF projectStartFrom IS NOT NULL THEN
        SET @SQLText = CONCAT(@SQLText, ' AND YEAR(p.start_date) >= ', projectStartFrom);
    END IF;

    IF projectStartTo IS NOT NULL THEN
        SET @SQLText = CONCAT(@SQLText, ' AND YEAR(p.start_date) <= ', projectStartTo);
    END IF;

    IF budgetFrom IS NOT NULL THEN
        SET @SQLText = CONCAT(@SQLText, ' AND p.budget_org >= ', budgetFrom);
    END IF;

    IF budgetTo IS NOT NULL THEN
        SET @SQLText = CONCAT(@SQLText, ' AND p.budget_org <= ', budgetTo);
    END IF;

    IF regionIds IS NOT NULL THEN
        SET @SQLText = CONCAT(@SQLText, ' AND pr.region_id IN (', regionIds, ')');
    END IF;

    IF countryIds IS NOT NULL THEN
        SET @SQLText = CONCAT(@SQLText, ' AND cp.country_id IN (', countryIds, ')');
    END IF;

    IF categoryIds IS NOT NULL THEN
        SET @SQLText = CONCAT(@SQLText, ' AND p.initiative_category_id IN (', categoryIds, ')');
    END IF;

    -- debug message
    SELECT @SQLText FROM DUAL;


    -- execute dynamic SQL
    PREPARE stmt FROM @SQLText;
    EXECUTE stmt;
    DEALLOCATE PREPARE stmt;


    -- find latest assessments of projects (yours)
    INSERT INTO dashboard_assessment (dashboard_id, assessment_id)
    SELECT dashboardYoursId, MAX(id) AS latest_assessment_id
    FROM assessments
    WHERE project_id IN
          (SELECT project_id FROM dashboard_project where dashboard_id = dashboardYoursId)
    GROUP BY project_id;


    -- -------------------------
    -- FIND PROJECTS AND LATEST ASSESSMENTS (OTHERS)
    -- -------------------------


    -- find all project ids (others)
    -- construct dynamic SQL

    SET @SQLText = '';
    SET @SQLText = CONCAT(@SQLText, ' INSERT INTO dashboard_project (dashboard_id, project_id)');
    SET @SQLText = CONCAT(@SQLText, ' SELECT ', dashboardOthersId, ', p.id');
    SET @SQLText = CONCAT(@SQLText, ' FROM projects p');
    SET @SQLText = CONCAT(@SQLText, ' LEFT JOIN portfolios po');
    SET @SQLText = CONCAT(@SQLText, ' ON p.portfolio_id = po.id');
    SET @SQLText = CONCAT(@SQLText, ' LEFT JOIN project_region pr');
    SET @SQLText = CONCAT(@SQLText, ' ON p.id = pr.project_id');
    SET @SQLText = CONCAT(@SQLText, ' LEFT JOIN country_project cp');
    SET @SQLText = CONCAT(@SQLText, ' ON p.id = cp.project_id');
    SET @SQLText = CONCAT(@SQLText, ' WHERE p.deleted_at IS NULL');
    SET @SQLText = CONCAT(@SQLText, ' AND po.contributes_to_funding_flow = 1');


    -- criteria
    IF organisationId IS NOT NULL THEN
        SET @SQLText = CONCAT(@SQLText, ' AND p.organisation_id != ', organisationId);
    END IF;

    IF projectStartFrom IS NOT NULL THEN
        SET @SQLText = CONCAT(@SQLText, ' AND YEAR(p.start_date) >= ', projectStartFrom);
    END IF;

    IF projectStartTo IS NOT NULL THEN
        SET @SQLText = CONCAT(@SQLText, ' AND YEAR(p.start_date) <= ', projectStartTo);
    END IF;

    IF budgetFrom IS NOT NULL THEN
        SET @SQLText = CONCAT(@SQLText, ' AND p.budget_org >= ', budgetFrom);
    END IF;

    IF budgetTo IS NOT NULL THEN
        SET @SQLText = CONCAT(@SQLText, ' AND p.budget_org <= ', budgetTo);
    END IF;

    IF regionIds IS NOT NULL THEN
        SET @SQLText = CONCAT(@SQLText, ' AND pr.region_id IN (', regionIds, ')');
    END IF;

    IF countryIds IS NOT NULL THEN
        SET @SQLText = CONCAT(@SQLText, ' AND cp.country_id IN (', countryIds, ')');
    END IF;

    IF categoryIds IS NOT NULL THEN
        SET @SQLText = CONCAT(@SQLText, ' AND p.initiative_category_id IN (', categoryIds, ')');
    END IF;

    -- execute dynamic SQL
    PREPARE stmt FROM @SQLText;
    EXECUTE stmt;
    DEALLOCATE PREPARE stmt;

    -- find latest assessments of projects (others)
    INSERT INTO dashboard_assessment (dashboard_id, assessment_id)
    SELECT dashboardOthersId, MAX(id) AS latest_assessment_id
    FROM assessments
    WHERE project_id IN
          (SELECT project_id FROM dashboard_project where dashboard_id = dashboardOthersId)
    GROUP BY project_id;

    -- -------------------------
    -- CHECK THERE ARE ENOUGH 'OTHER' PROJECTS TO BE CONSIDERED ANONYMOUS
    -- -------------------------


    -- This section takes the runtime from ~150ms to ~2.5s. Is there a way of optimising this?

#     SELECT COUNT(*) INTO ssOtherProjectCount from dashboard_project where dashboard_id = dashboardOthersId;

#     SELECT COUNT(DISTINCT (projects.organisation_id))
#     INTO ssOtherOrganisationCount
#     FROM dashboard_project
#              LEFT JOIN projects on projects.id = dashboard_project.project_id;
#

    -- find number of projects
    SELECT COUNT(DISTINCT project_id) AS number_of_project
    FROM dashboard_project
    WHERE dashboard_id = dashboardOthersId;

    -- find number of organisations
    SELECT COUNT(DISTINCT p.organisation_id) AS number_of_organisation
    FROM dashboard_project AS dp,
         projects AS p
    WHERE dp.project_id = p.id
      AND dp.dashboard_id = dashboardOthersId;

    #     -- Must have 10 or more projects
#     IF (ssOtherProjectCount < 10 OR ssOtherOrganisationCount < 3) THEN
#         SET tooFewOtherProjects = 1;
#     ELSE
    SET tooFewOtherProjects = 0;
    #     END IF;


    -- -------------------------
    -- PROJECT STATUS SUMMARY
    -- -------------------------


    -- CREATED PROJECTS
    SELECT COUNT(*), IFNULL(SUM(budget_org), 0)
    INTO ssCreatedCount, ssCreatedBudget
    FROM projects
    WHERE id IN
          (SELECT project_id FROM dashboard_project where dashboard_id = dashboardYoursId);

    SET ssCreatedPercent = 100;


    IF ssCreatedCount = 0 THEN
        SET status = 1001;
        SET message = 'There is no initiative found';

        DELETE FROM dashboard_result WHERE dashboard_id = dashboardYoursId;

        INSERT INTO dashboard_result (dashboard_id, dashboard_others_id, status, message, created_at, updated_at)
            VALUE (dashboardYoursId, dashboardOthersId, status, message, NOW(), NOW());
    END IF;


    IF ssCreatedCount > 0 THEN
        -- PASSED ALL REDLINES
        -- find number of projects that passed all red lines
        SELECT COUNT(*)
        INTO ssPassedAllCount
        FROM (SELECT assessment_id
              FROM assessment_red_line
              WHERE assessment_id IN
                    (SELECT assessment_id FROM dashboard_assessment WHERE dashboard_id = dashboardYoursId)
              GROUP BY assessment_id
              HAVING SUM(value) = 0) AS passed_all_red_lines;

        -- find budget that passed all red lines
        SELECT IFNULL(SUM(budget_org), 0)
        INTO ssPassedAllBudget
        FROM projects
        WHERE id IN
              (SELECT project_id
               FROM assessments
               WHERE id IN
                     (SELECT assessment_id
                      FROM assessment_red_line
                      WHERE assessment_id IN
                            (SELECT assessment_id FROM dashboard_assessment WHERE dashboard_id = dashboardYoursId)
                      GROUP BY assessment_id
                      HAVING SUM(value) = 0));

        SET ssPassedAllPercent = ssPassedAllCount / ssCreatedCount * 100;


        -- FAILED AT LEAST ONE RED LINE
        -- find number of projects that failed at least one red line
        SELECT COUNT(*)
        INTO ssFailedAnyCount
        FROM (SELECT assessment_id
              FROM assessment_red_line
              WHERE assessment_id IN
                    (SELECT assessment_id FROM dashboard_assessment WHERE dashboard_id = dashboardYoursId)
              GROUP BY assessment_id
              HAVING SUM(value) > 0) AS failed_any_red_lines;

        -- find budget that failed at least one red line
        SELECT IFNULL(SUM(budget_org), 0)
        INTO ssFailedAnyBudget
        FROM projects
        WHERE id IN
              (SELECT project_id
               FROM assessments
               WHERE id IN
                     (SELECT assessment_id
                      FROM assessment_red_line
                      WHERE assessment_id IN
                            (SELECT assessment_id FROM dashboard_assessment WHERE dashboard_id = dashboardYoursId)
                      GROUP BY assessment_id
                      HAVING SUM(value) > 0));

        SET ssFailedAnyPercent = ssFailedAnyCount / ssCreatedCount * 100;


        -- FULLY ASSESSED
        -- number of latest assessments that fully assessed
        SELECT COUNT(*)
        INTO ssFullyAssessedCount
        FROM assessments
        WHERE (assessments.redline_status = 'Failed'
          OR assessments.principle_status = 'Complete')
          AND id IN
              (SELECT assessment_id FROM dashboard_assessment WHERE dashboard_id = dashboardYoursId);

        -- budget for fully assessed projects
        SELECT IFNULL(SUM(budget_org), 0)
        INTO ssFullyAssessedBudget
        FROM projects
        WHERE id IN
              (SELECT project_id
               from assessments
               WHERE (assessments.redline_status = 'Failed'
                 OR assessments.principle_status = 'Complete')
                 AND id IN
                     (SELECT assessment_id FROM dashboard_assessment where dashboard_id = dashboardYoursId));

        SET ssFullyAssessedPercent = ssFullyAssessedCount / ssCreatedCount * 100;


        -- debug message
        SELECT ssCreatedCount,
               ssCreatedPercent,
               ssCreatedBudget,
               ssPassedAllCount,
               ssPassedAllPercent,
               ssPassedAllBudget,
               ssFailedAnyCount,
               ssFailedAnyPercent,
               ssFailedAnyBudget,
               ssFullyAssessedCount,
               ssFullyAssessedPercent,
               ssFullyAssessedBudget;


        -- construct status summary
        SET statusSummary = CONCAT('[',
                                   '{\"status\":\"Passed all red flags\",\"number\":', ssPassedAllCount,
                                   ',\"percent\":', ssPassedAllPercent, ',\"budget\":\"', FORMAT(ssPassedAllBudget, 0),
                                   '\"},',
                                    '{\"status\":\"Failed at least one red flag\",\"number\":', ssFailedAnyCount,
                                     ',\"percent\":', ssFailedAnyPercent, ',\"budget\":\"', FORMAT(ssFailedAnyBudget, 0),
                                    '\"},',
                                   '{\"status\":\"Fully assessed\",\"number\":', ssFullyAssessedCount, ',\"percent\":',
                                   ssFullyAssessedPercent, ',\"budget\":\"', FORMAT(ssFullyAssessedBudget, 0), '\"}]');

        SET totalCount = ssCreatedCount;
        SET totalBudget = ssCreatedBudget;


        -- -------------------------
        -- RED LINES SUMMARY
        -- -------------------------

        SELECT COUNT(*)
        INTO rsRedLineCount
        FROM assessment_red_line
        WHERE assessment_id IN
              (SELECT assessment_id FROM dashboard_assessment WHERE dashboard_id = dashboardYoursId)
          AND value IS NOT NULL;


        IF rsRedLineCount = 0 THEN
            SET status = 1002;
            SET message = 'There is no red flag reviewed';

            DELETE FROM dashboard_result WHERE dashboard_id = dashboardYoursId;

            INSERT INTO dashboard_result (dashboard_id, dashboard_others_id, status, message, status_summary,
                                          created_at, updated_at)
                VALUE (dashboardYoursId, dashboardOthersId, status, message, statusSummary, NOW(), NOW());
        END IF;


        IF rsRedLineCount > 0 THEN

            -- generate redlines summary (yours)
            INSERT INTO dashboard_red_line (dashboard_id, red_line_id, percentage)
            SELECT dashboardYoursId, red_line_id, 100 - ROUND((SUM(value) / COUNT(*) * 100), 2) AS percentage
            FROM assessment_red_line
            WHERE assessment_id IN
                  (SELECT assessment_id FROM dashboard_assessment WHERE dashboard_id = dashboardYoursId)
            GROUP BY red_line_id
            ORDER BY red_line_id;


            -- generate redlines summary (others)
            INSERT INTO dashboard_red_line (dashboard_id, red_line_id, percentage)
            SELECT dashboardOthersId, red_line_id, 100 - ROUND((SUM(value) / COUNT(*) * 100), 2) AS percentage
            FROM assessment_red_line
            WHERE assessment_id IN
                  (SELECT assessment_id FROM dashboard_assessment WHERE dashboard_id = dashboardOthersId)
            GROUP BY red_line_id
            ORDER BY red_line_id;


            -- construct red lines summary
            SET redlinesSummary = '[';

            -- open cursor
            OPEN rsCursor;

            -- loop
            rs_read_loop:
            LOOP

                -- fetch one record from cursor
                FETCH rsCursor INTO rsRedLineId, rsRedLineName, rsYourPercentage, rsOthersPercentage;

                -- special handling to avoid rounded value from 99.5 to 99.99 to a misleading 100
                IF rsYourPercentage BETWEEN 99.5 AND 99.99 THEN
                    SET rsYourFinalPercentage = 99.9;
                ELSE
                    SET rsYourFinalPercentage = ROUND(rsYourPercentage, 0);
                END IF;

                IF rsOthersPercentage BETWEEN 99.5 AND 99.99 THEN
                    SET rsOthersFinalPercentage = 99.9;
                ELSE
                    SET rsOthersFinalPercentage = ROUND(rsOthersPercentage, 0);
                END IF;

                -- exit loop if it is end of cursor
                IF rsDone THEN
                    LEAVE rs_read_loop;
                END IF;

                SET redlinesSummary =
                    CONCAT(redlinesSummary, '{\"id\":', rsRedLineId, ',\"name\":\"', rsRedLineName, '\",\"yours\":',
                           rsYourFinalPercentage, ',\"others\":', rsOthersFinalPercentage, '},');

                -- end loop
            END LOOP rs_read_loop;

            -- close cursor
            CLOSE rsCursor;

            -- remove the last comma
            SET redlinesSummary = SUBSTR(redlinesSummary, 1, LENGTH(redlinesSummary) - 1);

            SET redlinesSummary = CONCAT(redlinesSummary, ']');


            -- -------------------------
            -- PRINCIPLES SUMMARY
            -- -------------------------

            -- in table dashboard_assessment, remove latest assessment records if assessment.redline_status is not Complete
            CALL remove_incomplete_dashboard_assessment(dashboardYoursId);
            CALL remove_incomplete_dashboard_assessment(dashboardOthersId);


            SELECT COUNT(*)
            INTO psPrincipleCount
            FROM principle_assessment
            WHERE rating IS NOT NULL
              AND assessment_id IN
                  (SELECT assessment_id FROM dashboard_assessment WHERE dashboard_id = dashboardYoursId);

            IF psPrincipleCount = 0 THEN
                SET status = 1003;
                SET message = 'There is no principle assessed';

                DELETE FROM dashboard_result WHERE dashboard_id = dashboardYoursId;

                INSERT INTO dashboard_result
                SET dashboard_id        = dashboardYoursId,
                    dashboard_others_id = dashboardOthersId,
                    status              = status,
                    message             = message,
                    status_summary      = statusSummary,
                    red_lines_summary   = redlinesSummary,
                    created_at          = NOW(),
                    updated_at          = NOW();
            END IF;


            IF psPrincipleCount > 0 THEN

                -- generate principle assessment subtotal (yours)
                INSERT INTO dashboard_principle (dashboard_id, principle_id, category, counter)
                SELECT dashboardYoursId, principle_id, category, SUM(counter) AS counter
                FROM (SELECT ta.*, tb.category
                      FROM (SELECT principle_id, rating, COUNT(*) AS counter
                            FROM principle_assessment
                            WHERE rating IS NOT NULL
                              AND assessment_id IN
                                  (SELECT assessment_id FROM dashboard_assessment WHERE dashboard_id = dashboardYoursId)
                            GROUP BY principle_id, rating) AS ta,
                           dashboard_rating AS tb
                      WHERE ta.rating >= tb.min_rating
                        AND ta.rating < tb.max_rating
                      ORDER BY principle_id) AS principle_summary
                GROUP BY principle_id, category
                ORDER BY principle_id, category;

                -- generate principle assessment subtotal (others)
                INSERT INTO dashboard_principle (dashboard_id, principle_id, category, counter)
                SELECT dashboardOthersId, principle_id, category, SUM(counter) AS counter
                FROM (SELECT ta.*, tb.category
                      FROM (SELECT principle_id, rating, COUNT(*) AS counter
                            FROM principle_assessment
                            WHERE rating IS NOT NULL
                              AND assessment_id IN (SELECT assessment_id
                                                    FROM dashboard_assessment
                                                    WHERE dashboard_id = dashboardOthersId)
                            GROUP BY principle_id, rating) AS ta,
                           dashboard_rating AS tb
                      WHERE ta.rating >= tb.min_rating
                        AND ta.rating < tb.max_rating
                      ORDER BY principle_id) AS principle_summary
                GROUP BY principle_id, category
                ORDER BY principle_id, category;


                -- generate principle summary (yours)
                SET yoursPrinciplesSummary = generate_principles_summary(dashboardYoursId);

                -- generate principle summary (others)
                SET othersPrinciplesSummary = generate_principles_summary(dashboardOthersId);


                -- -------------------------
                -- STORING DASHBOARD RESULT
                -- -------------------------

                INSERT INTO dashboard_result
                SET dashboard_id              = dashboardYoursId,
                    dashboard_others_id       = dashboardOthersId,
                    status                    = status,
                    message                   = message,
                    total_count               = totalCount,
                    total_budget              = totalBudget,
                    too_few_others            = tooFewOtherProjects,
                    status_summary            = statusSummary,
                    red_lines_summary         = redlinesSummary,
                    principles_summary_yours  = yoursPrinciplesSummary,
                    principles_summary_others = othersPrinciplesSummary,
                    created_at                = NOW(),
                    updated_at                = NOW();

            END IF;

        END IF;

    END IF;

END
