USE blis_revamp;

INSERT INTO user(username, password, actualname, level, created_by, lab_config_id, email, phone, lang_id) VALUES ('tema_tech1', '56cbdfb7197c476fdd872cf2872f38131d24c8be', '', 0, 340, 151, '', '', 'default');
UPDATE lab_config SET name='Tema Polyclinic Laboratory', location='Ghana', admin_user_id=340, id_mode=1 WHERE lab_config_id=151;
