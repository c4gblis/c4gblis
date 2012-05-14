USE blis_129;

INSERT INTO user_rating (user_id, rating) VALUES (116, 6);
INSERT INTO user_rating (user_id, rating) VALUES (116, 6);
UPDATE test_type SET target_tat=40 WHERE test_type_id=90;
DELETE FROM reference_range WHERE measure_id=98;
UPDATE measure SET name='ASLO', range=':', unit='IU/ml' WHERE measure_id=98;
UPDATE test_type SET name='ASLO', description='Antistreptolysin o', clinical_data='', test_category_id='1', hide_patient_name='0', prevalence_threshold=70, target_tat=60 WHERE test_type_id=92;
INSERT INTO reference_range (measure_id, age_min, age_max, sex, range_lower, range_upper) VALUES (98, '0', '100', 'B', '200', '400');
INSERT INTO patient_daily (datestring, count) VALUES ('20120301', 1);
INSERT INTO specimen_session(session_num, count) VALUES ('20120301', 1);
INSERT INTO `patient`(`patient_id`, `addl_id`, `name`, `age`, `sex`, `partial_dob`, `surr_id`, `created_by`, `hash_value`,`ts`) VALUES (16971, '', 'Sw/05/kg', 0, 'M', '1982-03-01', '00381', 116, '88699c1eac34a90d5c41db0b6fdea0308f6f985f', '2012-03-01 00:00:00');
update patient_daily set count=2 where datestring='20120301' ;
UPDATE specimen_session SET count=2 WHERE session_num='20120301';
update patient_daily set count=3 where datestring='20120301' ;
UPDATE specimen_session SET count=3 WHERE session_num='20120301';
BEGIN;
INSERT INTO specimen (
			specimen_id, 
			patient_id, 
			specimen_type_id, 
			date_collected, 
			date_recvd, 
			user_id, 
			status_code_id, 
			referred_to, 
			comments, 
			aux_id,
			session_num, 
			time_collected, 
			report_to, 
			doctor, 
			referred_to_name, 
			daily_num
		) 
		VALUES (
			40489, 
			8570, 
			7, 
			'2012-03-01', 
			'2012-03-01', 
			116, 
			0, 
			0, 
			'', 
			'', 
			'20120301-3', 
			'12:14', 
			1, 
			'', 
			'', 
			'20120301-3'
		);
INSERT INTO test (
			test_type_id, 
			specimen_id, 
			result, 
			comments,
			verified_by,
			user_id ) 
		VALUES (
			12,  
			40489, 
			'', 
			'', 
			0, 
			116 );
INSERT INTO test (
			test_type_id, 
			specimen_id, 
			result, 
			comments,
			verified_by,
			user_id ) 
		VALUES (
			14,  
			40489, 
			'', 
			'', 
			0, 
			116 );
COMMIT;
UPDATE test SET result='3,df286509d490a358b5efe0f3231810adbcad468a', comments='', user_id=116, ts='2012-03-01 00:00:00' WHERE test_id=44730 ;
UPDATE test SET result='2.0,df286509d490a358b5efe0f3231810adbcad468a', comments='', user_id=116, ts='2012-03-01 00:00:00' WHERE test_id=44731 ;
UPDATE specimen SET status_code_id=1 WHERE specimen_id=40489;
INSERT INTO user_rating (user_id, rating) VALUES (116, 6);
INSERT INTO user_rating (user_id, rating) VALUES (116, 6);
INSERT INTO user_rating (user_id, rating) VALUES (116, 6);
INSERT INTO patient_daily (datestring, count) VALUES ('20120303', 1);
INSERT INTO specimen_session(session_num, count) VALUES ('20120303', 1);
INSERT INTO `patient`(`patient_id`, `addl_id`, `name`, `age`, `sex`, `partial_dob`, `surr_id`, `created_by`, `hash_value`,`ts`) VALUES (16972, '', 'March3 Test1', 0, 'M', '2011-03-03', '', 116, 'a9e8b24b7679beb3c12c6633ff0bad2181bbef14', '2012-03-03 00:00:00');
BEGIN;
INSERT INTO specimen (
			specimen_id, 
			patient_id, 
			specimen_type_id, 
			date_collected, 
			date_recvd, 
			user_id, 
			status_code_id, 
			referred_to, 
			comments, 
			aux_id,
			session_num, 
			time_collected, 
			report_to, 
			doctor, 
			referred_to_name, 
			daily_num
		) 
		VALUES (
			40490, 
			16972, 
			6, 
			'2012-03-03', 
			'2012-03-03', 
			116, 
			0, 
			0, 
			'', 
			'', 
			'20120303-1', 
			'19:49', 
			1, 
			'', 
			'', 
			'20120303-1'
		);
INSERT INTO test (
			test_type_id, 
			specimen_id, 
			result, 
			comments,
			verified_by,
			user_id ) 
		VALUES (
			63,  
			40490, 
			'', 
			'', 
			0, 
			116 );
INSERT INTO test (
			test_type_id, 
			specimen_id, 
			result, 
			comments,
			verified_by,
			user_id ) 
		VALUES (
			38,  
			40490, 
			'', 
			'', 
			0, 
			116 );
COMMIT;
BEGIN;
INSERT INTO specimen (
			specimen_id, 
			patient_id, 
			specimen_type_id, 
			date_collected, 
			date_recvd, 
			user_id, 
			status_code_id, 
			referred_to, 
			comments, 
			aux_id,
			session_num, 
			time_collected, 
			report_to, 
			doctor, 
			referred_to_name, 
			daily_num
		) 
		VALUES (
			40492, 
			16972, 
			11, 
			'2012-03-03', 
			'2012-03-03', 
			116, 
			0, 
			0, 
			'', 
			'', 
			'20120303-1', 
			'19:49', 
			1, 
			'', 
			'', 
			'20120303-1'
		);
INSERT INTO test (
			test_type_id, 
			specimen_id, 
			result, 
			comments,
			verified_by,
			user_id ) 
		VALUES (
			90,  
			40492, 
			'', 
			'', 
			0, 
			116 );
INSERT INTO test (
			test_type_id, 
			specimen_id, 
			result, 
			comments,
			verified_by,
			user_id ) 
		VALUES (
			91,  
			40492, 
			'', 
			'', 
			0, 
			116 );
COMMIT;
UPDATE test SET result='O,Rh +ve,,a9e8b24b7679beb3c12c6633ff0bad2181bbef14', comments='Blood Group:O', user_id=116, ts='2012-03-03 19:50:03' WHERE test_id=44732 ;
UPDATE test SET result='Positive,,a9e8b24b7679beb3c12c6633ff0bad2181bbef14', comments='Blood Group', user_id=116, ts='2012-03-03 19:50:03' WHERE test_id=44733 ;
UPDATE specimen SET status_code_id=1 WHERE specimen_id=40490;
UPDATE test SET result='Negative,,a9e8b24b7679beb3c12c6633ff0bad2181bbef14', comments='Albumin:Negative', user_id=116, ts='2012-03-03 19:50:25' WHERE test_id=44734 ;
UPDATE test SET result='Negative,,a9e8b24b7679beb3c12c6633ff0bad2181bbef14', comments='Albumin:NegativeSugar:Negative', user_id=116, ts='2012-03-03 19:50:26' WHERE test_id=44735 ;
UPDATE specimen SET status_code_id=1 WHERE specimen_id=40492;
INSERT INTO user_rating (user_id, rating) VALUES (116, 6);
UPDATE measure SET name='AFB', range='N/P', unit='' WHERE measure_id=89;
UPDATE test_type SET name='AFB', description='', clinical_data='', test_category_id='10', hide_patient_name='0', prevalence_threshold=70, target_tat=48 WHERE test_type_id=85;
INSERT INTO user_rating (user_id, rating) VALUES (116, 6);
UPDATE report_config SET header='Worksheet - Blood Urea Nitrogen??left', footer='-End-#', title='WK', margins='5,0,5,0', landscape=0, p_fields='0,1,0,1,1,0,0,0', s_fields='1,0,1,1,0,0,0', t_fields='1,0,1,0,0,0,0,1,0,0', p_custom_fields='', s_custom_fields='' WHERE report_id=9;
