DROP PROCEDURE IF EXISTS `remove_incomplete_dashboard_assessment`;

CREATE PROCEDURE `remove_incomplete_dashboard_assessment`(
	dashboardId INT
)
BEGIN

	-- variables for latest assessment
	DECLARE psAssessmentId INT;

	-- variable to determine whether it is end of cursor
	DECLARE psDone INT DEFAULT FALSE;

	-- cursor to find latest assessment with non "Complete" redline_status in table dashboard_assessment
	DECLARE psCursor CURSOR FOR
	SELECT tb.id
	FROM dashboard_assessment ta, assessments tb
	WHERE ta.assessment_id = tb.id
	AND ta.dashboard_id = dashboardId
	AND tb.redline_status != 'Complete';

	-- handler declaration
	DECLARE CONTINUE HANDLER FOR NOT FOUND SET psDone = TRUE;


	-- open cursor
	OPEN psCursor;

	-- loop
	ps_read_loop: LOOP

		-- fetch one record from cursor
		FETCH psCursor INTO psAssessmentId;

		DELETE FROM dashboard_assessment
		WHERE dashboard_id = dashboardId
		AND assessment_id = psAssessmentId;

		-- exit loop if it is end of cursor
		IF psDone THEN
		  	LEAVE ps_read_loop;
		END IF;

	-- end loop
	END LOOP ps_read_loop;

	-- close cursor
	CLOSE psCursor;

	COMMIT;

END
