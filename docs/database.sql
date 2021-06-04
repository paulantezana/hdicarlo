CREATE TABLE identity_document_types (
    identity_document_id INT AUTO_INCREMENT NOT NULL,
    nubefact_code VARCHAR(2) NOT NULL,
    description VARCHAR(255) NOT NULL,
    code VARCHAR(16) DEFAULT '',
    state TINYINT DEFAULT 1,
    CONSTRAINT pk_identity_document_types PRIMARY KEY (identity_document_id)
) ENGINE=InnoDB;

CREATE TABLE app_payment_intervals(
    app_payment_interval_id INT AUTO_INCREMENT NOT NULL,
    description VARCHAR(32) DEFAULT '',
    date_interval VARCHAR(3) DEFAULT 'M',
    state TINYINT DEFAULT 1,
    CONSTRAINT pk_app_payment_intervals PRIMARY KEY (app_payment_interval_id)
) ENGINE=InnoDB;

CREATE TABLE app_plans(
    app_plan_id INT AUTO_INCREMENT NOT NULL,
    description VARCHAR(255) DEFAULT '',

    updated_at DATETIME,
    created_at DATETIME,
    created_user_id INT,
    updated_user_id INT,
    state TINYINT DEFAULT 1,
    CONSTRAINT pk_app_plans PRIMARY KEY (app_plan_id)
) ENGINE=InnoDB;

CREATE TABLE app_plan_intervals(
    app_plan_interval_id INT AUTO_INCREMENT NOT NULL,
    app_plan_id INT NOT NULL,
    app_payment_interval_id INT NOT NULL,
    price DOUBLE(11,2) DEFAULT 0.00,

    updated_at DATETIME,
    created_at DATETIME,
    created_user_id INT,
    updated_user_id INT,
    state TINYINT DEFAULT 1,
    CONSTRAINT pk_app_plan_intervals PRIMARY KEY (app_plan_interval_id)
) ENGINE=InnoDB;

CREATE TABLE companies(
    company_id INT AUTO_INCREMENT NOT NULL,
    document_number VARCHAR(32) DEFAULT '',
    social_reason VARCHAR(255) DEFAULT '',
    commercial_reason VARCHAR(255) DEFAULT '',
    representative VARCHAR(128) DEFAULT '',
    logo VARCHAR(128) DEFAULT '',
    logo_large VARCHAR(128) DEFAULT '',
    phone VARCHAR(32) DEFAULT '',
    telephone VARCHAR(32) DEFAULT '',
    email VARCHAR(64) DEFAULT '',
    fiscal_address VARCHAR(255) DEFAULT '',
    url_web VARCHAR(255) DEFAULT '',

    development TINYINT DEFAULT 1,
    app_plan_id INT NOT NULL,
    contract_date_of_issue DATE,
    app_payment_interval_id INT NOT NULL,

    updated_at DATETIME,
    created_at DATETIME,
    created_user_id INT,
    updated_user_id INT,
    state TINYINT DEFAULT 1,
    CONSTRAINT pk_companies PRIMARY KEY (company_id)
) ENGINE=InnoDB;

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

    company_id INT NOT NULL,

    updated_at DATETIME,
    created_at DATETIME,
    created_user_id INT,
    updated_user_id INT,
    state TINYINT DEFAULT 1,
    CONSTRAINT pk_user_roles PRIMARY KEY (user_role_id),
    CONSTRAINT fk_user_roles_companies FOREIGN KEY (company_id) REFERENCES companies (company_id)
        ON UPDATE RESTRICT ON DELETE RESTRICT
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
    last_name VARCHAR(255) NOT NULL,
    gender enum('0','1','2') DEFAULT '2',
    avatar VARCHAR(64) DEFAULT '',
    email  VARCHAR(64) DEFAULT '' UNIQUE,
    identity_document_number VARCHAR(25) DEFAULT '',
    phone  VARCHAR(32) DEFAULT '',
    is_verified TINYINT DEFAULT 0,
    date_verified DATETIME,
    is_inner TINYINT DEFAULT 0,

    identity_document_id INT NOT NULL,
    user_role_id INT NOT NULL,
    company_id INT NOT NULL,

    updated_at DATETIME,
    created_at DATETIME,
    created_user_id INT,
    updated_user_id INT,
    state TINYINT DEFAULT 1,
    CONSTRAINT pk_user PRIMARY KEY (user_id),
    CONSTRAINT fk_user_identity_document_types FOREIGN KEY (identity_document_id) REFERENCES identity_document_types (identity_document_id)
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

CREATE TABLE app_payments(
    app_payment_id INT AUTO_INCREMENT NOT NULL,
    description VARCHAR(255) DEFAULT '',
    date_time_of_issue DATETIME NOT NULL,
    reference VARCHAR(32) DEFAULT '',
    number INT DEFAULT 0,
    is_last TINYINT DEFAULT 1,

    canceled TINYINT DEFAULT 0,
    canceled_message VARCHAR(64) DEFAULT '',

    from_date_time  DATE NOT NULL,
    to_date_time  DATE NOT NULL,
    total FLOAT(8,2) NOT NULL,

    user_id INT NOT NULL,
    company_id INT NOT NULL,

    updated_at DATETIME,
    created_at DATETIME,
    created_user_id INT,
    updated_user_id INT,
    CONSTRAINT pk_app_contracts PRIMARY KEY (app_payment_id),
    CONSTRAINT fk_app_contracts_users FOREIGN KEY (user_id) REFERENCES users (user_id)
        ON UPDATE RESTRICT ON DELETE RESTRICT,
    CONSTRAINT fk_app_contracts_companies FOREIGN KEY (company_id) REFERENCES companies (company_id)
        ON UPDATE RESTRICT ON DELETE RESTRICT
) ENGINE=InnoDB;

-- //
CREATE TABLE countries (
   country_id INT AUTO_INCREMENT NOT NULL,
   name varchar(50) DEFAULT '',
   code VARCHAR(16) DEFAULT '',
   state TINYINT DEFAULT 1,
   CONSTRAINT pk_country PRIMARY KEY (country_id)
) ENGINE=InnoDB;

CREATE TABLE geo_level_1 (
   geo_level_1_id INT AUTO_INCREMENT NOT NULL,
   name varchar(50) DEFAULT '',
   country_id INT NOT NULL,
   state TINYINT DEFAULT 1,
   CONSTRAINT pk_geo_level_1 PRIMARY KEY (geo_level_1_id)
) ENGINE=InnoDB;

CREATE TABLE geo_level_2 (
   geo_level_2_id INT AUTO_INCREMENT NOT NULL,
   name varchar(50) DEFAULT '',
   geo_level_1_id INT NOT NULL,
   state TINYINT DEFAULT 1,
   CONSTRAINT pk_geo_level_2 PRIMARY KEY (geo_level_2_id)
) ENGINE=InnoDB;

CREATE TABLE geo_level_3 (
   geo_level_3_id INT AUTO_INCREMENT NOT NULL,
   name varchar(50) DEFAULT '',
   geo_level_2_id INT NOT NULL,
   state TINYINT DEFAULT 1,
   CONSTRAINT pk_geo_level_3 PRIMARY KEY (geo_level_3_id)
) ENGINE=InnoDB;

CREATE TABLE geo_locations (
    geo_location_id INT AUTO_INCREMENT NOT NULL,
    country_id INT NOT NULL,
    geo_level_1_id INT NOT NULL,
    geo_level_2_id INT NOT NULL,
    geo_level_3_id INT NOT NULL,

    state TINYINT DEFAULT 1,
    CONSTRAINT pk_geo_locations PRIMARY KEY (geo_location_id)
) ENGINE=InnoDB;

CREATE TABLE geo_label_locations (
    geo_label_location_id INT AUTO_INCREMENT NOT NULL,
    denomination VARCHAR(50) NOT NULL,
    level_number INT NOT NULL,
    country_id INT NOT NULL,
    CONSTRAINT pk_geo_label_locations PRIMARY KEY (geo_label_location_id)
) ENGINE=InnoDB;


-- DATABASE
CREATE TABLE customers(
    customer_id INT AUTO_INCREMENT NOT NULL,

    document_number VARCHAR(16) NOT NULL,
    identity_document_id INT NOT NULL,
    social_reason VARCHAR(255),
    commercial_reason VARCHAR(255),
    fiscal_address VARCHAR(255),
    email VARCHAR(64),
    telephone VARCHAR(255),

    company_id INT NOT NULL,

    updated_at DATETIME,
    created_at DATETIME,
    created_user_id INT,
    updated_user_id INT,
    state TINYINT DEFAULT 1,
    CONSTRAINT pk_customers PRIMARY KEY (customer_id),
    CONSTRAINT fk_customers_identity_document_types FOREIGN KEY (identity_document_id) REFERENCES identity_document_types (identity_document_id)
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

CREATE TABLE exhibitor_states(
    exhibitor_state_id INT AUTO_INCREMENT NOT NULL,
    description VARCHAR(64) NOT NULL,

    updated_at DATETIME,
    created_at DATETIME,
    created_user_id INT,
    updated_user_id INT,
    state TINYINT DEFAULT 1,
    CONSTRAINT pk_exhibitor_states PRIMARY KEY (exhibitor_state_id)
) ENGINE=InnoDB;

CREATE TABLE exhibitors(
    exhibitor_id INT AUTO_INCREMENT NOT NULL,
    code VARCHAR(64) NOT NULL,
    address VARCHAR(255) NOT NULL,
    operative VARCHAR(64) DEFAULT '',

    geo_location_id INT NOT NULL,
    lat_long VARCHAR(32) DEFAULT '',

    customer_id INT NOT NULL,
    size_id INT NOT NULL,
    company_id INT NOT NULL,

    updated_at DATETIME,
    created_at DATETIME,
    created_user_id INT,
    updated_user_id INT,
    state TINYINT DEFAULT 1,
    CONSTRAINT pk_exhibitors PRIMARY KEY (exhibitor_id),
    CONSTRAINT uk_exhibitors UNIQUE (code, company_id)
) ENGINE=InnoDB;


CREATE TABLE exhibitor_histories(
    exhibitor_history_id INT AUTO_INCREMENT NOT NULL,
    is_last TINYINT NOT NULL,
    exhibitor_state VARCHAR(32) DEFAULT '',
    time_of_issue DATETIME,

    exhibitor_id INT NOT NULL,
    user_id INT NOT NULL,
    exhibitor_state_id INT DEFAULT 0,

    updated_at DATETIME,
    created_at DATETIME,
    created_user_id INT,
    updated_user_id INT,
    state TINYINT DEFAULT 1,
    CONSTRAINT pk_exhibitor_histories PRIMARY KEY (exhibitor_history_id)
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

    company_id INT NOT NULL,
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

    company_id INT NOT NULL,
    exhibitor_id INT NOT NULL,
    user_id INT NOT NULL,

    updated_at DATETIME,
    created_at DATETIME,
    created_user_id INT,
    updated_user_id INT,
    state TINYINT DEFAULT 1,
    CONSTRAINT pk_deliveries PRIMARY KEY (deliveriy_id)
) ENGINE=InnoDB;

INSERT INTO app_authorizations (module,action,description,state)
VALUES ('home','home','dashboard',true),

       ('rol','listar','listar roles',true),
       ('rol','crear','crear nuevos rol',true),
       ('rol','eliminar','Eliminar un rol',true),
       ('rol','modificar','Acualizar los roles',true),

       ('user','listar','listar usuarios',true),
       ('user','crear','crear nuevo usuarios',true),
       ('user','eliminar','Eliminar un usuario',true),
       ('user','modificar','Acualizar los datos del usuario exepto la contraseña',true),
       ('user','modificarContraseña','Solo se permite actualizar la contraseña',true),

       ('company','listar','listar roles',true),
       ('company','modificar','Actualizar empresa',true),

       ('customer','listar','listar clientes',true),
       ('customer','crear','crear nuevos cliente',true),
       ('customer','eliminar','Eliminar un cliente',true),
       ('customer','modificar','Acualizar los clientes',true),

       ('exhibitor','listar','listar clientes',true),
       ('exhibitor','crear','crear nuevos cliente',true),
       ('exhibitor','eliminar','Eliminar un cliente',true),
       ('exhibitor','modificar','Acualizar los clientes',true);


INSERT INTO app_payment_intervals(description, date_interval) VALUES ('Mensual','M'),
                                ('Trimestral','3M'),
                                ('Semestral','6M'),
                                ('Anual','Y');
-- DATA TYPE
INSERT INTO identity_document_types(nubefact_code, description, code) VALUES
('1', 'DNI','DNI'),
('4', 'CARNET DE EXTRANJERIA','CARD'),
('6', 'RUC','RUC'),
('7', 'PASAPORTE','PAS');

INSERT INTO sizes(description) VALUES ('Pequeño'),('Mediano'),('Grande');

INSERT INTO users (user_name, password, email, full_name, last_name, is_inner, identity_document_id, user_role_id, company_id)
            VALUES('inner@admin','$2y$10$AHqA.v5m6X9/b4gNwJH6CuIHbr/U.t5IKGxoS4ZfnLhASDO87duEu','inner@admin','','',1,1,0,0); -- password admin

-- VIEW
CREATE VIEW geo_location_view AS

SELECT geo.geo_location_id, geo.country_id,
    CONCAT(IFNULL(geo1.name,''), '-', IFNULL(geo2.name,''),'-',IFNULL(geo3.name,'')) AS geo_name,
    COALESCE(geo3.name, geo2.name, geo1.name) AS last_geo_name
FROM geo_locations AS geo 
INNER JOIN geo_level_1 AS geo1 ON geo.geo_level_1_id = geo1.geo_level_1_id AND geo1.state = 1
LEFT JOIN geo_level_2 AS geo2 ON geo.geo_level_2_id = geo2.geo_level_2_id AND geo2.state = 1
LEFT JOIN geo_level_3 AS geo3 ON geo.geo_level_3_id = geo3.geo_level_3_id AND geo3.state = 1
WHERE geo.state = 1