<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // For correct indentation in MySQL stored procedure to be created,
        // MySQL program source code needs to be stored in below format

        $procedure =
"
DROP PROCEDURE IF EXISTS `generate_dashboard_summary`;

CREATE DEFINER=`root`@`localhost` PROCEDURE `generate_dashboard_summary`(
	IN dashboardYoursId INT,
	IN dashboardOthersId INT,
	IN organisationId INT,
	IN portfolioId INT,
	IN regionId INT,
	IN countryId INT,
	IN projectStartFrom INT,
	IN projectStartTo INT,
	IN budgetFrom INT,
	IN budgetTo INT,
	OUT status INT,
	OUT message VARCHAR(1000),
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
	DECLARE rsYourPercentage INT;
	DECLARE rsOthersPercentage INT;
	DECLARE rsYoursOverallPercentage FLOAT;
	DECLARE rsOthersOverallPercentage FLOAT;

	-- variables for principles summary
	DECLARE psPrincipleCount INT;


	-- variable to determine whether it is end of cursor
	DECLARE rsDone INT DEFAULT FALSE;

	-- cursor to join yours, others, redlines as redlines summary
	DECLARE rsCursor CURSOR FOR
	SELECT ta.red_line_id, tc.name, ta.percentage AS yours_percentage, tb.percentage AS others_percentage FROM
	(SELECT * FROM dashboard_red_line WHERE dashboard_id = dashboardYoursId) AS ta,
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
    SET @SQLText = CONCAT(@SQLText, ' WHERE p.id = p.id');


    -- criteria
    IF organisationId IS NOT NULL THEN
    	SET @SQLText = CONCAT(@SQLText, ' AND p.organisation_id = ', organisationId);
    END IF;

	IF portfolioId IS NOT NULL THEN
    	SET @SQLText = CONCAT(@SQLText, ' AND p.portfolio_id = ', portfolioId);
    END IF;

    IF projectStartFrom IS NOT NULL AND projectStartTo IS NOT NULL THEN
		SET @SQLText = CONCAT(@SQLText, ' AND YEAR(p.start_date) BETWEEN ', projectStartFrom , ' AND ', projectStartTo);
    END IF;

    IF budgetFrom IS NOT NULL AND budgetTo IS NOT NULL THEN
    	SET @SQLText = CONCAT(@SQLText, ' AND p.budget BETWEEN ', budgetFrom, ' AND ', budgetTo);
    END IF;

    IF regionId IS NOT NULL THEN
    	SET @SQLText = CONCAT(@SQLText, ' AND pr.region_id = ', regionId);
    END IF;

    IF countryId IS NOT NULL THEN
    	SET @SQLText = CONCAT(@SQLText, ' AND cp.country_id = ', countryId);
    END IF;

    -- debug message
    -- SELECT @SQLText FROM DUAL;


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
    SET @SQLText = CONCAT(@SQLText, ' LEFT JOIN project_region pr');
    SET @SQLText = CONCAT(@SQLText, ' ON p.id = pr.project_id');
    SET @SQLText = CONCAT(@SQLText, ' LEFT JOIN country_project cp');
    SET @SQLText = CONCAT(@SQLText, ' ON p.id = cp.project_id');
    SET @SQLText = CONCAT(@SQLText, ' WHERE p.id = p.id');


    -- criteria
    IF projectStartFrom IS NOT NULL AND projectStartTo IS NOT NULL THEN
		SET @SQLText = CONCAT(@SQLText, ' AND YEAR(p.start_date) BETWEEN ', projectStartFrom , ' AND ', projectStartTo);
    END IF;

    IF budgetFrom IS NOT NULL AND budgetTo IS NOT NULL THEN
    	SET @SQLText = CONCAT(@SQLText, ' AND p.budget BETWEEN ', budgetFrom, ' AND ', budgetTo);
    END IF;

    IF regionId IS NOT NULL THEN
    	SET @SQLText = CONCAT(@SQLText, ' AND pr.region_id = ', regionId);
    END IF;

    IF countryId IS NOT NULL THEN
    	SET @SQLText = CONCAT(@SQLText, ' AND cp.country_id = ', countryId);
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
	-- PROJECT STATUS SUMMARY
	-- -------------------------


	-- CREATED PROJECTS
	SELECT COUNT(*), IFNULL(SUM(budget), 0)
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
		FROM
		(SELECT assessment_id FROM assessment_red_line WHERE assessment_id IN
			(SELECT assessment_id FROM dashboard_assessment WHERE dashboard_id = dashboardYoursId)
		GROUP BY assessment_id
		HAVING SUM(value) = 0) AS passed_all_red_lines;

		-- find budget that passed all red lines
		SELECT IFNULL(SUM(budget), 0)
		INTO ssPassedAllBudget
		FROM projects
		WHERE id IN
			(SELECT project_id FROM assessments WHERE id IN
				(SELECT assessment_id FROM assessment_red_line WHERE assessment_id IN
					(SELECT assessment_id FROM dashboard_assessment WHERE dashboard_id = dashboardYoursId)
				GROUP BY assessment_id
				HAVING SUM(value) = 0
				)
			);

		SET ssPassedAllPercent = ssPassedAllCount / ssCreatedCount * 100;


		-- FAILED AT LEAST ONE RED LINE
		-- find number of projects that failed at least one red line
		SELECT COUNT(*)
		INTO ssFailedAnyCount
		FROM
		(SELECT assessment_id FROM assessment_red_line WHERE assessment_id IN
			(SELECT assessment_id FROM dashboard_assessment WHERE dashboard_id = dashboardYoursId)
		GROUP BY assessment_id
		HAVING SUM(value) > 0) AS failed_any_red_lines;

		-- find budget that failed at least one red line
		SELECT IFNULL(SUM(budget), 0)
		INTO ssFailedAnyBudget
		FROM projects
		WHERE id IN
			(SELECT project_id FROM assessments WHERE id IN
				(SELECT assessment_id FROM assessment_red_line WHERE assessment_id IN
					(SELECT assessment_id FROM dashboard_assessment WHERE dashboard_id = dashboardYoursId)
				GROUP BY assessment_id
				HAVING SUM(value) > 0
				)
			);

		SET ssFailedAnyPercent = ssFailedAnyCount / ssCreatedCount * 100;


		-- FULLY ASSESSED
		-- number of latest assessments that fully assessed
		SELECT COUNT(*)
		INTO ssFullyAssessedCount
		FROM assessments
		WHERE completed_at IS NOT NULL
		AND id IN
		(SELECT assessment_id FROM dashboard_assessment WHERE dashboard_id = dashboardYoursId);

		-- budget for fully assessed projects
		SELECT IFNULL(SUM(budget), 0)
		INTO ssFullyAssessedBudget
		FROM projects
		WHERE id IN
			(SELECT project_id from assessments
			WHERE completed_at IS NOT NULL
			AND id IN
				(SELECT assessment_id FROM dashboard_assessment where dashboard_id = dashboardYoursId)
			);

		SET ssFullyAssessedPercent = ssFullyAssessedCount / ssCreatedCount * 100;


		-- debug message
		SELECT ssCreatedCount, ssCreatedPercent, ssCreatedBudget, ssPassedAllCount, ssPassedAllPercent, ssPassedAllBudget,
		ssFailedAnyCount, ssFailedAnyPercent, ssFailedAnyBudget, ssFullyAssessedCount, ssFullyAssessedPercent, ssFullyAssessedBudget;


		-- construct status summary
		SET statusSummary = CONCAT('[{\"status\":\"Created\",\"number\":', ssCreatedCount, ',\"percent\":', ssCreatedPercent, ',\"budget\":\"', FORMAT(ssCreatedBudget, 0), '\"},',
							 '{\"status\":\"Passed all redlines\",\"number\":', ssPassedAllCount, ',\"percent\":', ssPassedAllPercent, ',\"budget\":\"', FORMAT(ssPassedAllBudget, 0), '\"},',
							 '{\"status\":\"Failed at least 1 redline\",\"number\":', ssFailedAnyCount, ',\"percent\":', ssFailedAnyPercent, ',\"budget\":\"', FORMAT(ssFailedAnyBudget, 0), '\"},',
							 '{\"status\":\"Fully assessed\",\"number\":', ssFullyAssessedCount, ',\"percent\":', ssFullyAssessedPercent, ',\"budget\":\"', FORMAT(ssFullyAssessedBudget, 0), '\"}]');



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
			SET message = 'There is no red line reviewed';

			DELETE FROM dashboard_result WHERE dashboard_id = dashboardYoursId;

			INSERT INTO dashboard_result (dashboard_id, dashboard_others_id, status, message, status_summary, created_at, updated_at)
			VALUE (dashboardYoursId, dashboardOthersId, status, message, statusSummary, NOW(), NOW());
		END IF;


		IF rsRedLineCount > 0 THEN

			-- generate redlines summary (yours)
			INSERT INTO dashboard_red_line (dashboard_id, red_line_id, percentage)
			SELECT dashboardYoursId, red_line_id, 100 - ROUND((SUM(value) / COUNT(*) * 100), 0) AS percentage
			FROM assessment_red_line
			WHERE assessment_id IN
				(SELECT assessment_id FROM dashboard_assessment WHERE dashboard_id = dashboardYoursId)
			GROUP BY red_line_id
			ORDER BY red_line_id;


			-- generate redlines summary (others)
			INSERT INTO dashboard_red_line (dashboard_id, red_line_id, percentage)
			SELECT dashboardOthersId, red_line_id, 100 - ROUND((SUM(value) / COUNT(*) * 100), 0) AS percentage
			FROM assessment_red_line
			WHERE assessment_id IN
				(SELECT assessment_id FROM dashboard_assessment WHERE dashboard_id = dashboardOthersId)
			GROUP BY red_line_id
			ORDER BY red_line_id;


			-- find yours overall percentage and others overall percentage
			SELECT ROUND(AVG(ta.percentage), 1) AS yours_overall, ROUND(AVG(tb.percentage), 1) AS others_overall
			INTO rsYoursOverallPercentage, rsOthersOverallPercentage
			FROM
			(SELECT * FROM dashboard_red_line WHERE dashboard_id = dashboardYoursId) AS ta,
			(SELECT * FROM dashboard_red_line WHERE dashboard_id = dashboardOthersId) AS tb
			WHERE ta.red_line_id = tb.red_line_id;


			-- construct red lines summary
			SET redlinesSummary = '[';

			-- open cursor
			OPEN rsCursor;

			-- loop
			rs_read_loop: LOOP

				-- fetch one record from cursor
				FETCH rsCursor INTO rsRedLineId, rsRedLineName, rsYourPercentage, rsOthersPercentage;

				-- exit loop if it is end of cursor
				IF rsDone THEN
				  	LEAVE rs_read_loop;
				END IF;

				SET redlinesSummary = CONCAT(redlinesSummary, '{\"id\":', rsRedLineId, ',\"name\":\"', rsRedLineName, '\",\"yours\":', rsYourPercentage, ',\"others\":', rsOthersPercentage, '},');

			-- end loop
			END LOOP rs_read_loop;

			-- close cursor
			CLOSE rsCursor;

			-- add overall percentage
			SET redlinesSummary = CONCAT(redlinesSummary, '{\"id\":-1,\"name\":\"Overall\", \"yours\":', rsYoursOverallPercentage, ',\"others\":', rsOthersOverallPercentage, '}');

			SET redlinesSummary = CONCAT(redlinesSummary, ']');



			-- -------------------------
			-- PRINCIPLES SUMMARY
			-- -------------------------

			SELECT COUNT(*)
			INTO psPrincipleCount
			FROM principle_assessment
			WHERE rating IS NOT NULL
			AND assessment_id IN (SELECT assessment_id FROM dashboard_assessment WHERE dashboard_id = dashboardYoursId);

			IF psPrincipleCount = 0 THEN
				SET status = 1003;
				SET message = 'There is no principle assessed';

				DELETE FROM dashboard_result WHERE dashboard_id = dashboardYoursId;

				INSERT INTO dashboard_result (dashboard_id, dashboard_others_id, status, message, status_summary, red_lines_summary, created_at, updated_at)
				VALUE (dashboardYoursId, dashboardOthersId, status, message, statusSummary, redlinesSummary, NOW(), NOW());
			END IF;


			IF psPrincipleCount > 0 THEN

				-- generate principle assessment subtotal (yours)
				INSERT INTO dashboard_principle (dashboard_id, principle_id, category, counter)
				SELECT dashboardYoursId, principle_id, category, SUM(counter) AS counter FROM
				(SELECT ta.*, tb.category FROM
				(SELECT principle_id, rating, COUNT(*) AS counter FROM principle_assessment
				WHERE rating IS NOT NULL
				AND assessment_id IN (SELECT assessment_id FROM dashboard_assessment WHERE dashboard_id = dashboardYoursId)
				GROUP BY principle_id, rating) AS ta, dashboard_rating AS tb
				WHERE ta.rating >= tb.min_rating
				AND ta.rating < tb.max_rating
				ORDER BY principle_id) AS principle_summary
				GROUP BY principle_id, category
				ORDER BY principle_id, category;

				-- generate principle assessment subtotal (others)
				INSERT INTO dashboard_principle (dashboard_id, principle_id, category, counter)
				SELECT dashboardOthersId, principle_id, category, SUM(counter) AS counter FROM
				(SELECT ta.*, tb.category FROM
				(SELECT principle_id, rating, COUNT(*) AS counter FROM principle_assessment
				WHERE rating IS NOT NULL
				AND assessment_id IN (SELECT assessment_id FROM dashboard_assessment WHERE dashboard_id = dashboardOthersId)
				GROUP BY principle_id, rating) AS ta, dashboard_rating AS tb
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

				INSERT INTO dashboard_result (dashboard_id, dashboard_others_id, status, message, status_summary, red_lines_summary, principles_summary_yours, principles_summary_others, created_at, updated_at)
				VALUE (dashboardYoursId, dashboardOthersId, status, message, statusSummary, redlinesSummary, yoursPrinciplesSummary, othersPrinciplesSummary, NOW(), NOW());

			END IF;

		END IF;

	END IF;

END
";

        DB::unprepared($procedure);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $procedure = "DROP PROCEDURE IF EXISTS `generate_dashboard_summary` ";

        DB::unprepared($procedure);
    }
};
