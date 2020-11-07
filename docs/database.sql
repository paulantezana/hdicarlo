CREATE TABLE app_authorizations(
    app_authorization_id INT AUTO_INCREMENT NOT NULL,
    module VARCHAR(64) NOT NULL,
    action VARCHAR(64) DEFAULT '',
    description VARCHAR(64) DEFAULT '',
    state TINYINT DEFAULT 1,
    CONSTRAINT pk_app_authorizations PRIMARY KEY (app_authorization_id)
) ENGINE=InnoDB;

CREATE TABLE user_roles(
    user_role_id INT AUTO_INCREMENT NOT NULL,
    description VARCHAR(64) NOT NULL,

    updated_at DATETIME,
    created_at DATETIME,
    created_user_id INT,
    updated_user_id INT,
    state TINYINT DEFAULT 1,
    CONSTRAINT pk_user_roles PRIMARY KEY (user_role_id)
) ENGINE=InnoDB;

CREATE TABLE user_role_authorizations(
    user_role_id INT NOT NULL,
    app_authorization_id INT NOT NULL,
    CONSTRAINT fk_user_roles_authorization_user_roles FOREIGN KEY (user_role_id) REFERENCES user_roles (user_role_id)
        ON UPDATE RESTRICT ON DELETE RESTRICT,
    CONSTRAINT fk_user_roles_authorization_app_authorizations FOREIGN KEY (app_authorization_id) REFERENCES app_authorizations (app_authorization_id)
        ON UPDATE RESTRICT ON DELETE RESTRICT
) ENGINE=InnoDB;

CREATE TABLE users(
    user_id INT AUTO_INCREMENT NOT NULL,
    user_name VARCHAR(64) NOT NULL UNIQUE,
    password VARCHAR(64) NOT NULL,
    full_name VARCHAR(255) NOT NULL,
    gender enum('0','1','2') DEFAULT '2',
    avatar VARCHAR(64) DEFAULT '',
    email  VARCHAR(64) DEFAULT '' UNIQUE,
    user_role_id INT NOT NULL,
    phone  VARCHAR(32) DEFAULT '',
    is_verified TINYINT DEFAULT 0,
    date_verified DATETIME,

    updated_at DATETIME,
    created_at DATETIME,
    created_user_id INT,
    updated_user_id INT,
    state TINYINT DEFAULT 1,
    CONSTRAINT pk_user PRIMARY KEY (user_id),
    CONSTRAINT fk_user_user_roles FOREIGN KEY (user_role_id) REFERENCES user_roles (user_role_id)
        ON UPDATE RESTRICT ON DELETE RESTRICT
) ENGINE=InnoDB;

CREATE TABLE user_forgots(
    user_forgot_id INT AUTO_INCREMENT NOT NULL,
    user_id INT NOT NULL,
    secret_key VARCHAR(128) NOT NULL,
    used TINYINT DEFAULT 0,

    updated_at DATETIME,
    created_at DATETIME,
    created_user_id INT,
    updated_user_id INT,
    state TINYINT DEFAULT 1,
    CONSTRAINT pk_user_forgots PRIMARY KEY (user_forgot_id),
    CONSTRAINT fk_user_forgots_user FOREIGN KEY (user_id) REFERENCES users (user_id)
        ON UPDATE RESTRICT ON DELETE RESTRICT
) ENGINE=InnoDB;

INSERT INTO app_authorizations (module,action,description,state)
VALUES ('home','home','dashboard',true),

       ('rol','listar','listar roles',true),
       ('rol','crear','crear nuevos rol',true),
       ('rol','eliminar','Eliminar un rol',true),
       ('rol','modificar','Acualizar los roles',true),

       ('usuario','listar','listar usuarios',true),
       ('usuario','crear','crear nuevo usuarios',true),
       ('usuario','eliminar','Eliminar un usuario',true),
       ('usuario','modificar','Acualizar los datos del usuario exepto la contraseña',true),
       ('usuario','modificarContraseña','Solo se permite actualizar la contraseña',true);

INSERT INTO user_roles (created_at, created_user_id, description, state)
VALUES ('2020-02-17 00:00:00', '0', 'Usuario', 1),
       ('2020-02-17 00:00:00', '0', 'Administrador', 1);

INSERT INTO users(user_name, password, full_name, avatar, email, user_role_id, gender)
VALUES ('admin1',sha1('admin1'),'admin1','','admin@admin.com',2,2);

INSERT INTO user_role_authorizations (user_role_id,app_authorization_id)
VALUES (2, 1),
       (2, 2),
       (2, 3),
       (2, 4),
       (2, 5),
       (2, 6),
       (2, 7),
       (2, 8),
       (2, 9),
       (2, 10);

INSERT INTO user_role_authorizations (user_role_id,app_authorization_id)
VALUES (1, 1),
       (1, 6),
       (1, 7),
       (1, 8),
       (1, 9),
       (1, 10);


-- UTILS
CREATE TABLE identity_document_types(
    code VARCHAR(1) NOT NULL,
    description VARCHAR(255) NOT NULL,
    state TINYINT DEFAULT 1,
    CONSTRAINT pk_identity_document_types PRIMARY KEY (code)
) ENGINE=InnoDB;

CREATE TABLE countries (
   country_id INT AUTO_INCREMENT NOT NULL,
   name varchar(50) NOT NULL,
   code VARCHAR(32) NOT NULL,
   latlong VARCHAR(32) DEFAULT '',

   updated_at DATETIME,
   created_at DATETIME,
   created_user_id INT,
   updated_user_id INT,
   state TINYINT DEFAULT 1,
   CONSTRAINT pk_countries PRIMARY KEY (country_id)
) ENGINE=InnoDB;

CREATE TABLE geo_level_1 (
   geo_level_1_id INT AUTO_INCREMENT NOT NULL,
   name varchar(50) NOT NULL,
   latlong VARCHAR(32) DEFAULT '',
   
   country_id INT NOT NULL,

   updated_at DATETIME,
   created_at DATETIME,
   created_user_id INT,
   updated_user_id INT,
   state TINYINT DEFAULT 1,
   CONSTRAINT pk_geo_level_1 PRIMARY KEY (geo_level_1_id)
) ENGINE=InnoDB;

CREATE TABLE geo_level_2 (
   geo_level_2_id INT AUTO_INCREMENT NOT NULL,
   name varchar(50) NOT NULL,
   geo_level_1_id INT NOT NULL,
   latlong VARCHAR(32) DEFAULT '',

   updated_at DATETIME,
   created_at DATETIME,
   created_user_id INT,
   updated_user_id INT,
   state TINYINT DEFAULT 1,
   CONSTRAINT pk_geo_level_2 PRIMARY KEY (geo_level_2_id)
) ENGINE=InnoDB;

CREATE TABLE geo_level_3 (
   geo_level_3_id INT AUTO_INCREMENT NOT NULL,
   name varchar(50) NOT NULL,
   geo_level_2_id INT NOT NULL,
   latlong VARCHAR(32) DEFAULT '',

   updated_at DATETIME,
   created_at DATETIME,
   created_user_id INT,
   updated_user_id INT,
   state TINYINT DEFAULT 1,
   CONSTRAINT pk_geo_level_3 PRIMARY KEY (geo_level_3_id)
) ENGINE=InnoDB;


-- DATABASE
CREATE TABLE customers(
    customer_id INT AUTO_INCREMENT NOT NULL,

    document_number VARCHAR(16) NOT NULL,
    identity_document_code VARCHAR(64) NOT NULL,
    social_reason VARCHAR(255),
    commercial_reason VARCHAR(255),
    fiscal_address VARCHAR(255),
    email VARCHAR(64),
    telephone VARCHAR(255),

    updated_at DATETIME,
    created_at DATETIME,
    created_user_id INT,
    updated_user_id INT,
    state TINYINT DEFAULT 1,
    CONSTRAINT pk_customers PRIMARY KEY (customer_id),
    CONSTRAINT fk_customers_identity_document_types FOREIGN KEY (identity_document_code) REFERENCES identity_document_types (code)
        ON UPDATE RESTRICT ON DELETE RESTRICT
) ENGINE=InnoDB;

CREATE TABLE sizes(
    size_id INT AUTO_INCREMENT NOT NULL,
    description VARCHAR(64) NOT NULL,

    updated_at DATETIME,
    created_at DATETIME,
    created_user_id INT,
    updated_user_id INT,
    state TINYINT DEFAULT 1,
    CONSTRAINT pk_sizes PRIMARY KEY (size_id)
) ENGINE=InnoDB;

CREATE TABLE exhibitors(
    exhibitor_id INT AUTO_INCREMENT NOT NULL,
    code VARCHAR(64) NOT NULL UNIQUE,
    address VARCHAR(255) NOT NULL,
    operative VARCHAR(64) DEFAULT '',

    country_id INT DEFAULT 0,
    geo_level_1_id INT DEFAULT 0,
    geo_level_2_id INT DEFAULT 0,
    geo_level_3_id INT DEFAULT 0,
    lat_long VARCHAR(32) DEFAULT '',

    customer_id INT NOT NULL,
    size_id INT NOT NULL,

    updated_at DATETIME,
    created_at DATETIME,
    created_user_id INT,
    updated_user_id INT,
    state TINYINT DEFAULT 1,
    CONSTRAINT pk_exhibitors PRIMARY KEY (exhibitor_id)
) ENGINE=InnoDB;


CREATE TABLE exhibitor_states(
    exhibitor_state_id INT AUTO_INCREMENT NOT NULL,
    is_last TINYINT NOT NULL,
    exhibitor_state VARCHAR(32) DEFAULT '',
    time_of_issue DATETIME,

    exhibitor_id INT NOT NULL,
    user_id INT NOT NULL,

    updated_at DATETIME,
    created_at DATETIME,
    created_user_id INT,
    updated_user_id INT,
    state TINYINT DEFAULT 1,
    CONSTRAINT pk_exhibitors PRIMARY KEY (exhibitor_state_id)
) ENGINE=InnoDB;

CREATE TABLE exhibitor_maintenances(
    exhibitor_maintenance_id INT AUTO_INCREMENT NOT NULL,
    time_of_issue DATETIME,
    
    exhibitor_id INT NOT NULL,

    updated_at DATETIME,
    created_at DATETIME,
    created_user_id INT,
    updated_user_id INT,
    state TINYINT DEFAULT 1,
    CONSTRAINT pk_exhibitor_maintenances PRIMARY KEY (exhibitor_maintenance_id)
) ENGINE=InnoDB;

CREATE TABLE orders(
    order_id INT AUTO_INCREMENT NOT NULL,
    date_of_issue DATETIME,
    lat_long VARCHAR(32) DEFAULT '',
    picture_path VARCHAR(64) DEFAULT '',
    date_of_delivery DATE,
    observation VARCHAR(64) DEFAULT '',

    exhibitor_id INT NOT NULL,
    user_id INT NOT NULL,

    updated_at DATETIME,
    created_at DATETIME,
    created_user_id INT,
    updated_user_id INT,
    state TINYINT DEFAULT 1,
    CONSTRAINT pk_orders PRIMARY KEY (order_id)
) ENGINE=InnoDB;

CREATE TABLE deliveries(
    deliveriy_id INT AUTO_INCREMENT NOT NULL,
    date_of_issue DATETIME,
    lat_long VARCHAR(32) DEFAULT '',
    picture_path VARCHAR(64) DEFAULT '',
    date_of_delivery DATE,
    observation VARCHAR(64) DEFAULT '',

    exhibitor_id INT NOT NULL,
    user_id INT NOT NULL,

    updated_at DATETIME,
    created_at DATETIME,
    created_user_id INT,
    updated_user_id INT,
    state TINYINT DEFAULT 1,
    CONSTRAINT pk_deliveries PRIMARY KEY (deliveriy_id)
) ENGINE=InnoDB;

-- Attention_type
-- Attention



-- DATA TYPE
INSERT INTO identity_document_types(code, description) VALUES
('1', 'DNI'),
('4', 'CARNET DE EXTRANJERIA'),
('6', 'RUC'),
('7', 'PASAPORTE');


-- VIEW
CREATE VIEW geo_search AS

SELECT gl1.country_id, gl1.geo_level_1_id, 0 AS geo_level_2_id, 0 AS geo_level_3_id,
	CONCAT(IFNULL(gl1.name,'')) AS geo_name
FROM geo_level_1 AS gl1
WHERE gl1.state = 1

UNION ALL

SELECT gl1.country_id, gl1.geo_level_1_id, gl2.geo_level_2_id, 0 AS geo_level_3_id,
	CONCAT(IFNULL(gl2.name,''),'-', IFNULL(gl1.name,'')) AS geo_name
FROM geo_level_1 AS gl1
INNER JOIN geo_level_2 AS gl2 ON gl1.geo_level_1_id = gl2.geo_level_1_id AND gl2.state = 1
WHERE gl1.state = 1

UNION ALL

SELECT gl1.country_id, gl1.geo_level_1_id, gl2.geo_level_2_id, gl3.geo_level_3_id,
	CONCAT(IFNULL(gl3.name,''),'-', IFNULL(gl2.name,''),'-', IFNULL(gl1.name,'')) AS geo_name
FROM geo_level_1 AS gl1
INNER JOIN geo_level_2 AS gl2 ON gl1.geo_level_1_id = gl2.geo_level_1_id AND gl2.state = 1
INNER JOIN geo_level_3 AS gl3 ON gl2.geo_level_2_id = gl3.geo_level_2_id AND gl3.state = 1
WHERE gl1.state = 1;