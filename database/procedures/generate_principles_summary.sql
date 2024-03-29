DROP FUNCTION IF EXISTS `generate_principles_summary`;

CREATE DEFINER=`root`@`localhost` FUNCTION `generate_principles_summary`(
	dashboardId INT
) RETURNS varchar(16383) CHARSET utf8mb4
    DETERMINISTIC
	BEGIN

	-- variables for principles summary
	DECLARE psPrincipleId INT;
	DECLARE psPrincipleName VARCHAR(16383);
	DECLARE psGreenPercentage FLOAT;
	DECLARE psYellowPercentage FLOAT;
	DECLARE psRedPercentage FLOAT;

	DECLARE principlesSummary VARCHAR(16383);

	-- variable to determine whether it is end of cursor
	DECLARE psDone INT DEFAULT FALSE;

	-- cursor to generate principle summary with counter and percentage for green, yellow, red
	DECLARE psCursor CURSOR FOR
	SELECT p.id, p.name,
	-- these 4 columns are useful for debug, but not necessary to be showed in principle summary
	-- IFNULL(green.counter, 0) AS green_counter,
	-- IFNULL(yellow.counter, 0) AS yellow_counter,
	-- IFNULL(red.counter, 0) AS red_counter,
	-- IFNULL(green.counter, 0) + IFNULL(yellow.counter, 0) + IFNULL(red.counter, 0) AS total,
	IF(
		IFNULL(green.counter, 0) + IFNULL(yellow.counter, 0) + IFNULL(red.counter, 0) = 0,
		NULL,
		ROUND(IFNULL(green.counter, 0) / (IFNULL(green.counter, 0) + IFNULL(yellow.counter, 0) + IFNULL(red.counter, 0)) * 100, 2)
	) AS green_percentage,
	IF(
	IFNULL(green.counter, 0) + IFNULL(yellow.counter, 0) + IFNULL(red.counter, 0) = 0,
		NULL,

	ROUND(IFNULL(yellow.counter, 0) / (IFNULL(green.counter, 0) + IFNULL(yellow.counter, 0) + IFNULL(red.counter, 0)) * 100, 2)
	) AS yellow_percentage,
	IF(
		IFNULL(green.counter, 0) + IFNULL(yellow.counter, 0) + IFNULL(red.counter, 0) = 0,
		NULL,

	ROUND(IFNULL(red.counter, 0) / (IFNULL(green.counter, 0) + IFNULL(yellow.counter, 0) + IFNULL(red.counter, 0)) * 100, 2)
	) AS red_percentage
	FROM principles p
	LEFT JOIN
	(SELECT * FROM dashboard_principle WHERE dashboard_id = dashboardId AND category = 'GREEN') AS green
	ON p.id = green.principle_id
	LEFT JOIN
	(SELECT * FROM dashboard_principle WHERE dashboard_id = dashboardId AND category = 'YELLOW') AS yellow
	ON p.id = yellow.principle_id
	LEFT JOIN
	(SELECT * FROM dashboard_principle WHERE dashboard_id = dashboardId AND category = 'RED') AS red
	ON p.id = red.principle_id
	ORDER BY p.id;

	-- handler declaration
	DECLARE CONTINUE HANDLER FOR NOT FOUND SET psDone = TRUE;


	-- construct principles summary
	SET principlesSummary = '[';

	-- open cursor
	OPEN psCursor;

	-- loop
	ps_read_loop: LOOP

		-- fetch one record from cursor
		FETCH psCursor INTO psPrincipleId, psPrincipleName, psGreenPercentage, psYellowPercentage, psRedPercentage;

		-- exit loop if it is end of cursor
		IF psDone THEN
		  	LEAVE ps_read_loop;
		END IF;

		SET principlesSummary = CONCAT(principlesSummary, '{\"id\":', psPrincipleId, ',\"name\":\"', psPrincipleName, '\",\"green\":', COALESCE(psGreenPercentage, '\"na\"'), ',\"yellow\":', COALESCE(psYellowPercentage, '\"na\"'), ',\"red\":', COALESCE(psRedPercentage, '\"na\"'), '},');

	-- end loop
	END LOOP ps_read_loop;

	-- close cursor
	CLOSE psCursor;

	-- remove the last comma
	SET principlesSummary = SUBSTRING(principlesSummary, 1, LENGTH(principlesSummary)-1);

	SET principlesSummary = CONCAT(principlesSummary, ']');


	RETURN principlesSummary;

END
