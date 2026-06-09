-- ============================================================
-- Nectra Digital — SAFE v2 UPGRADE (phpMyAdmin)
-- Skips columns/indexes that already exist — no duplicate errors
-- Run ONCE on database that already has ge_* tables (v1 or partial v2)
-- ============================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- Helper: run ALTER only if column missing
-- ge_landing_pages.industry_id
SET @c := (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'ge_landing_pages' AND COLUMN_NAME = 'industry_id');
SET @s := IF(@c = 0, 'ALTER TABLE ge_landing_pages ADD COLUMN industry_id INT UNSIGNED NOT NULL DEFAULT 0 AFTER city_id', 'SELECT 1');
PREPARE st FROM @s; EXECUTE st; DEALLOCATE PREPARE st;

SET @c := (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'ge_landing_pages' AND COLUMN_NAME = 'page_type');
SET @s := IF(@c = 0, 'ALTER TABLE ge_landing_pages ADD COLUMN page_type ENUM(''service_city'',''service_city_industry'') NOT NULL DEFAULT ''service_city'' AFTER industry_id', 'SELECT 1');
PREPARE st FROM @s; EXECUTE st; DEALLOCATE PREPARE st;

SET @c := (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'ge_landing_pages' AND COLUMN_NAME = 'h3');
SET @s := IF(@c = 0, 'ALTER TABLE ge_landing_pages ADD COLUMN h3 VARCHAR(500) DEFAULT NULL AFTER h2', 'SELECT 1');
PREPARE st FROM @s; EXECUTE st; DEALLOCATE PREPARE st;

SET @c := (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'ge_landing_pages' AND COLUMN_NAME = 'cta_json');
SET @s := IF(@c = 0, 'ALTER TABLE ge_landing_pages ADD COLUMN cta_json JSON DEFAULT NULL AFTER internal_links_json', 'SELECT 1');
PREPARE st FROM @s; EXECUTE st; DEALLOCATE PREPARE st;

-- ge_keywords v2 columns
ALTER TABLE ge_keywords MODIFY COLUMN keyword_type ENUM(
    'primary','secondary','lsi','commercial','transactional','informational','local','long_tail','semantic'
) NOT NULL DEFAULT 'primary';

SET @c := (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'ge_keywords' AND COLUMN_NAME = 'industry_id');
SET @s := IF(@c = 0, 'ALTER TABLE ge_keywords ADD COLUMN industry_id INT UNSIGNED NOT NULL DEFAULT 0 AFTER city_id', 'SELECT 1');
PREPARE st FROM @s; EXECUTE st; DEALLOCATE PREPARE st;

SET @c := (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'ge_keywords' AND COLUMN_NAME = 'landing_page_id');
SET @s := IF(@c = 0, 'ALTER TABLE ge_keywords ADD COLUMN landing_page_id INT UNSIGNED DEFAULT NULL AFTER industry_id', 'SELECT 1');
PREPARE st FROM @s; EXECUTE st; DEALLOCATE PREPARE st;

-- New v2 tables
CREATE TABLE IF NOT EXISTS ge_industries (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL,
    description TEXT DEFAULT NULL,
    icon VARCHAR(100) DEFAULT 'fa-industry',
    meta_title_template VARCHAR(500) DEFAULT NULL,
    meta_description_template TEXT DEFAULT NULL,
    status ENUM('active','inactive') NOT NULL DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uk_ge_ind_slug (slug),
    INDEX idx_ge_ind_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS ge_authors (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL,
    title VARCHAR(255) DEFAULT NULL,
    bio TEXT DEFAULT NULL,
    expertise JSON DEFAULT NULL,
    avatar VARCHAR(500) DEFAULT NULL,
    linkedin VARCHAR(500) DEFAULT NULL,
    email VARCHAR(255) DEFAULT NULL,
    is_founder TINYINT(1) NOT NULL DEFAULT 0,
    schema_json JSON DEFAULT NULL,
    status ENUM('active','inactive') NOT NULL DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uk_ge_auth_slug (slug)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS ge_reviews (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    author_name VARCHAR(255) NOT NULL,
    author_title VARCHAR(255) DEFAULT NULL,
    rating TINYINT UNSIGNED NOT NULL DEFAULT 5,
    review_body TEXT NOT NULL,
    service_id INT UNSIGNED DEFAULT NULL,
    city_id INT UNSIGNED DEFAULT NULL,
    is_featured TINYINT(1) NOT NULL DEFAULT 0,
    status ENUM('published','pending') NOT NULL DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_ge_rev_service (service_id),
    INDEX idx_ge_rev_featured (is_featured)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS ge_crm_clients (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    company_name VARCHAR(255) NOT NULL,
    contact_name VARCHAR(255) DEFAULT NULL,
    email VARCHAR(255) NOT NULL,
    phone VARCHAR(50) DEFAULT NULL,
    city VARCHAR(100) DEFAULT NULL,
    industry_id INT UNSIGNED DEFAULT NULL,
    status ENUM('lead','active','inactive') NOT NULL DEFAULT 'lead',
    notes TEXT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_ge_client_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS ge_crm_leads (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    phone VARCHAR(50) DEFAULT NULL,
    service_interest VARCHAR(255) DEFAULT NULL,
    city VARCHAR(100) DEFAULT NULL,
    source VARCHAR(100) DEFAULT 'website',
    message TEXT DEFAULT NULL,
    status ENUM('new','contacted','qualified','proposal','won','lost') NOT NULL DEFAULT 'new',
    assigned_to VARCHAR(255) DEFAULT NULL,
    follow_up_at DATETIME DEFAULT NULL,
    client_id INT UNSIGNED DEFAULT NULL,
    meta_json JSON DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_ge_lead_status (status),
    INDEX idx_ge_lead_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS ge_crm_projects (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    client_id INT UNSIGNED NOT NULL,
    title VARCHAR(255) NOT NULL,
    service_id INT UNSIGNED DEFAULT NULL,
    description TEXT DEFAULT NULL,
    budget DECIMAL(12,2) DEFAULT NULL,
    status ENUM('planning','active','review','completed','on_hold') NOT NULL DEFAULT 'planning',
    start_date DATE DEFAULT NULL,
    end_date DATE DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_ge_proj_client (client_id),
    INDEX idx_ge_proj_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS ge_crm_proposals (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    lead_id INT UNSIGNED DEFAULT NULL,
    client_id INT UNSIGNED DEFAULT NULL,
    title VARCHAR(255) NOT NULL,
    content LONGTEXT DEFAULT NULL,
    amount DECIMAL(12,2) DEFAULT NULL,
    status ENUM('draft','sent','accepted','rejected') NOT NULL DEFAULT 'draft',
    valid_until DATE DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_ge_prop_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS ge_crm_quotations (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    proposal_id INT UNSIGNED DEFAULT NULL,
    lead_id INT UNSIGNED DEFAULT NULL,
    quote_number VARCHAR(50) NOT NULL,
    line_items JSON DEFAULT NULL,
    subtotal DECIMAL(12,2) DEFAULT 0,
    tax DECIMAL(12,2) DEFAULT 0,
    total DECIMAL(12,2) DEFAULT 0,
    status ENUM('draft','sent','accepted','expired') NOT NULL DEFAULT 'draft',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uk_ge_quote_num (quote_number)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS ge_tools (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL,
    tool_type ENUM('seo_audit','keyword','meta_generator','schema_generator','robots_generator','sitemap_generator','roi_calculator','cost_calculator','content_analyzer','custom') NOT NULL,
    description TEXT DEFAULT NULL,
    config_json JSON DEFAULT NULL,
    usage_count INT UNSIGNED NOT NULL DEFAULT 0,
    status ENUM('active','inactive') NOT NULL DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uk_ge_tools_slug (slug)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS ge_knowledge_base (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL,
    category VARCHAR(100) DEFAULT NULL,
    silo VARCHAR(100) DEFAULT NULL,
    content LONGTEXT DEFAULT NULL,
    quick_answer TEXT DEFAULT NULL,
    author_id INT UNSIGNED DEFAULT NULL,
    pillar_id INT UNSIGNED DEFAULT NULL,
    meta_title VARCHAR(255) DEFAULT NULL,
    meta_description TEXT DEFAULT NULL,
    faq_json JSON DEFAULT NULL,
    schema_json JSON DEFAULT NULL,
    status ENUM('published','draft') NOT NULL DEFAULT 'draft',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uk_ge_kb_slug (slug),
    INDEX idx_ge_kb_silo (silo),
    INDEX idx_ge_kb_pillar (pillar_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS ge_competitor_analyses (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    competitor_url VARCHAR(500) NOT NULL,
    domain VARCHAR(255) NOT NULL,
    meta_title VARCHAR(500) DEFAULT NULL,
    meta_description TEXT DEFAULT NULL,
    h1_tags JSON DEFAULT NULL,
    h2_tags JSON DEFAULT NULL,
    schemas_found JSON DEFAULT NULL,
    keywords_detected JSON DEFAULT NULL,
    content_gaps JSON DEFAULT NULL,
    opportunities JSON DEFAULT NULL,
    raw_analysis JSON DEFAULT NULL,
    analyzed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_ge_comp_domain (domain)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS ge_generation_queue (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    job_id INT UNSIGNED DEFAULT NULL,
    service_id INT UNSIGNED NOT NULL,
    city_id INT UNSIGNED NOT NULL,
    industry_id INT UNSIGNED NOT NULL DEFAULT 0,
    status ENUM('pending','processing','done','failed','skipped') NOT NULL DEFAULT 'pending',
    result_slug VARCHAR(500) DEFAULT NULL,
    error_message TEXT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    processed_at DATETIME DEFAULT NULL,
    INDEX idx_ge_gq_status (status),
    INDEX idx_ge_gq_job (job_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS ge_analytics_events (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    event_type VARCHAR(50) NOT NULL,
    page_url VARCHAR(500) DEFAULT NULL,
    landing_page_id INT UNSIGNED DEFAULT NULL,
    metadata JSON DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_ge_ae_type (event_type),
    INDEX idx_ge_ae_date (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Unique index: drop old, add combo (only if needed)
SET @idx := (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'ge_landing_pages' AND INDEX_NAME = 'uk_ge_lp_service_city');
SET @s := IF(@idx > 0, 'ALTER TABLE ge_landing_pages DROP INDEX uk_ge_lp_service_city', 'SELECT 1');
PREPARE st FROM @s; EXECUTE st; DEALLOCATE PREPARE st;

SET @idx := (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'ge_landing_pages' AND INDEX_NAME = 'uk_ge_lp_combo');
SET @s := IF(@idx = 0, 'ALTER TABLE ge_landing_pages ADD UNIQUE KEY uk_ge_lp_combo (service_id, city_id, industry_id)', 'SELECT 1');
PREPARE st FROM @s; EXECUTE st; DEALLOCATE PREPARE st;

-- Seed data
INSERT IGNORE INTO ge_settings (setting_key, setting_value) VALUES
('url_pattern_industry', '{url_prefix}-company-in-{city_slug}-for-{industry_slug}'),
('url_pattern_city', '{url_prefix}-company-in-{city_slug}'),
('platform_version', '2.0'),
('chatbot_enabled', '1'),
('chatbot_welcome', 'Hi! I am Nectra AI. How can I help you grow today?');

INSERT IGNORE INTO ge_tools (name, slug, tool_type, description, status) VALUES
('SEO Audit Tool', 'seo-audit', 'seo_audit', 'Analyze any URL for SEO issues', 'active'),
('Keyword Research Tool', 'keyword-research', 'keyword', 'Generate keyword ideas for your niche', 'active'),
('Meta Tag Generator', 'meta-tag-generator', 'meta_generator', 'Generate SEO meta tags instantly', 'active'),
('Schema Markup Generator', 'schema-generator', 'schema_generator', 'Build JSON-LD schema markup', 'active'),
('Robots.txt Generator', 'robots-generator', 'robots_generator', 'Create optimized robots.txt', 'active'),
('Sitemap Generator', 'sitemap-generator', 'sitemap_generator', 'Generate XML sitemap structure', 'active'),
('Marketing ROI Calculator', 'roi-calculator', 'roi_calculator', 'Calculate campaign ROI', 'active'),
('Website Cost Calculator', 'website-cost-calculator', 'cost_calculator', 'Estimate website development cost', 'active'),
('AI Content Analyzer', 'content-analyzer', 'content_analyzer', 'Analyze content for SEO and readability', 'active');

INSERT IGNORE INTO ge_authors (name, slug, title, bio, is_founder, linkedin, status) VALUES
('Ravindra Kumar Chauhan', 'ravindra-kumar-chauhan', 'Founder & CEO',
 'Founder & CEO of Nectra Digital with 5+ years experience in SEO, Digital Marketing, AI Automation, and Software Development.',
 1, 'https://www.linkedin.com/in/ravindra-kumar-chauhan', 'active');

SET FOREIGN_KEY_CHECKS = 1;

-- Done. Verify: SELECT COUNT(*) FROM ge_tools; SELECT * FROM ge_settings WHERE setting_key='platform_version';
