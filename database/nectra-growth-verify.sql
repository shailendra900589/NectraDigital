-- Run in phpMyAdmin SQL tab to verify database is ready

SHOW TABLES LIKE 'ge_%';

SELECT setting_key, setting_value FROM ge_settings WHERE setting_key IN ('platform_version','chatbot_enabled');

SELECT COUNT(*) AS tools_count FROM ge_tools;
SELECT COUNT(*) AS authors_count FROM ge_authors;
SELECT COUNT(*) AS industries_count FROM ge_industries;

SHOW COLUMNS FROM ge_landing_pages LIKE 'industry_id';
SHOW COLUMNS FROM ge_landing_pages LIKE 'cta_json';
