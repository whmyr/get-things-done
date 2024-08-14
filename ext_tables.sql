CREATE TABLE tx_getthingsdone_domain_model_task
(
    title varchar(255) NOT NULL DEFAULT '',
    description text,
    done tinyint(4) UNSIGNED NOT NULL DEFAULT 0,
    owner int(11) UNSIGNED NOT NULL DEFAULT 0,
    assigned_to int(11) UNSIGNED NOT NULL DEFAULT 0
);