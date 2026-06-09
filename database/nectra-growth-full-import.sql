-- ============================================================
-- Nectra Digital Growth Platform — FULL DATABASE IMPORT
-- phpMyAdmin / Hostinger: Select your DB → Import → Choose this file
-- Safe to re-run: uses CREATE IF NOT EXISTS + INSERT IGNORE
-- ============================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ============================================================
-- SERVICES
-- ============================================================
CREATE TABLE IF NOT EXISTS ge_services (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL,
    url_prefix VARCHAR(255) NOT NULL COMMENT 'URL segment e.g. seo, software-development',
    meta_title_template VARCHAR(500) DEFAULT NULL,
    meta_description_template TEXT DEFAULT NULL,
    h1_template VARCHAR(500) DEFAULT NULL,
    h2_template VARCHAR(500) DEFAULT NULL,
    content_template LONGTEXT DEFAULT NULL,
    service_image VARCHAR(500) DEFAULT NULL,
    faq_template JSON DEFAULT NULL,
    keywords_template TEXT DEFAULT NULL,
    schema_type VARCHAR(50) DEFAULT 'Service',
    sort_order INT DEFAULT 0,
    status ENUM('active','inactive') NOT NULL DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uk_ge_services_slug (slug),
    UNIQUE KEY uk_ge_services_url_prefix (url_prefix),
    INDEX idx_ge_services_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- CITIES
-- ============================================================
CREATE TABLE IF NOT EXISTS ge_cities (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL,
    state VARCHAR(255) DEFAULT NULL,
    country VARCHAR(100) NOT NULL DEFAULT 'India',
    population INT UNSIGNED DEFAULT 0,
    latitude DECIMAL(10,8) DEFAULT NULL,
    longitude DECIMAL(11,8) DEFAULT NULL,
    city_description TEXT DEFAULT NULL,
    status ENUM('active','inactive') NOT NULL DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uk_ge_cities_slug (slug),
    INDEX idx_ge_cities_status (status),
    INDEX idx_ge_cities_country_state (country, state)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- INDUSTRIES (v2)
-- ============================================================
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

-- ============================================================
-- KEYWORDS (v2)
-- ============================================================
CREATE TABLE IF NOT EXISTS ge_keywords (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    keyword VARCHAR(500) NOT NULL,
    keyword_type ENUM('primary','secondary','lsi','commercial','transactional','informational','local','long_tail','semantic') NOT NULL DEFAULT 'primary',
    service_id INT UNSIGNED DEFAULT NULL,
    city_id INT UNSIGNED DEFAULT NULL,
    industry_id INT UNSIGNED NOT NULL DEFAULT 0,
    landing_page_id INT UNSIGNED DEFAULT NULL,
    is_auto_generated TINYINT(1) NOT NULL DEFAULT 0,
    status ENUM('active','inactive') NOT NULL DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_ge_keywords_type (keyword_type),
    INDEX idx_ge_keywords_service (service_id),
    INDEX idx_ge_keywords_city (city_id),
    INDEX idx_ge_keywords_status (status),
    CONSTRAINT fk_ge_keywords_service FOREIGN KEY (service_id) REFERENCES ge_services(id) ON DELETE CASCADE,
    CONSTRAINT fk_ge_keywords_city FOREIGN KEY (city_id) REFERENCES ge_cities(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- LANDING PAGES (v2 — Service × City × Industry)
-- ============================================================
CREATE TABLE IF NOT EXISTS ge_landing_pages (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    service_id INT UNSIGNED NOT NULL,
    city_id INT UNSIGNED NOT NULL,
    industry_id INT UNSIGNED NOT NULL DEFAULT 0,
    page_type ENUM('service_city','service_city_industry') NOT NULL DEFAULT 'service_city',
    slug VARCHAR(500) NOT NULL,
    url_path VARCHAR(500) NOT NULL,
    meta_title VARCHAR(255) DEFAULT NULL,
    meta_description TEXT DEFAULT NULL,
    h1 VARCHAR(500) DEFAULT NULL,
    h2 VARCHAR(500) DEFAULT NULL,
    h3 VARCHAR(500) DEFAULT NULL,
    content LONGTEXT DEFAULT NULL,
    quick_answer TEXT DEFAULT NULL,
    key_takeaways TEXT DEFAULT NULL,
    summary TEXT DEFAULT NULL,
    expert_insight TEXT DEFAULT NULL,
    faq_json JSON DEFAULT NULL,
    schema_json JSON DEFAULT NULL,
    keywords_json JSON DEFAULT NULL,
    paa_json JSON DEFAULT NULL,
    voice_answer TEXT DEFAULT NULL,
    internal_links_json JSON DEFAULT NULL,
    cta_json JSON DEFAULT NULL,
    content_hash CHAR(64) DEFAULT NULL,
    is_indexed TINYINT(1) NOT NULL DEFAULT 0,
    index_status ENUM('pending','submitted','indexed','excluded','failed') NOT NULL DEFAULT 'pending',
    index_submitted_at DATETIME DEFAULT NULL,
    index_verified_at DATETIME DEFAULT NULL,
    status ENUM('published','draft','archived') NOT NULL DEFAULT 'published',
    generated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uk_ge_lp_slug (slug),
    UNIQUE KEY uk_ge_lp_combo (service_id, city_id, industry_id),
    INDEX idx_ge_lp_status (status),
    INDEX idx_ge_lp_index_status (index_status),
    INDEX idx_ge_lp_service (service_id),
    INDEX idx_ge_lp_city (city_id),
    CONSTRAINT fk_ge_lp_service FOREIGN KEY (service_id) REFERENCES ge_services(id) ON DELETE CASCADE,
    CONSTRAINT fk_ge_lp_city FOREIGN KEY (city_id) REFERENCES ge_cities(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- KEYWORD MAPPINGS
-- ============================================================
CREATE TABLE IF NOT EXISTS ge_keyword_mappings (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    landing_page_id INT UNSIGNED NOT NULL,
    keyword_id INT UNSIGNED NOT NULL,
    is_primary TINYINT(1) NOT NULL DEFAULT 0,
    position INT NOT NULL DEFAULT 0,
    UNIQUE KEY uk_ge_km_lp_kw (landing_page_id, keyword_id),
    INDEX idx_ge_km_lp (landing_page_id),
    CONSTRAINT fk_ge_km_lp FOREIGN KEY (landing_page_id) REFERENCES ge_landing_pages(id) ON DELETE CASCADE,
    CONSTRAINT fk_ge_km_kw FOREIGN KEY (keyword_id) REFERENCES ge_keywords(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- INDEXING QUEUE
-- ============================================================
CREATE TABLE IF NOT EXISTS ge_indexing_queue (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    landing_page_id INT UNSIGNED DEFAULT NULL,
    url VARCHAR(500) NOT NULL,
    action_type ENUM('submit','check','remove') NOT NULL DEFAULT 'submit',
    status ENUM('pending','processing','completed','failed') NOT NULL DEFAULT 'pending',
    response TEXT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    processed_at DATETIME DEFAULT NULL,
    INDEX idx_ge_iq_status (status),
    INDEX idx_ge_iq_lp (landing_page_id),
    CONSTRAINT fk_ge_iq_lp FOREIGN KEY (landing_page_id) REFERENCES ge_landing_pages(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- GENERATION JOBS
-- ============================================================
CREATE TABLE IF NOT EXISTS ge_generation_jobs (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    job_type ENUM('single','bulk','service_all_cities','city_all_services','full_matrix','regenerate') NOT NULL,
    service_id INT UNSIGNED DEFAULT NULL,
    city_id INT UNSIGNED DEFAULT NULL,
    total_pages INT UNSIGNED NOT NULL DEFAULT 0,
    processed INT UNSIGNED NOT NULL DEFAULT 0,
    failed INT UNSIGNED NOT NULL DEFAULT 0,
    status ENUM('queued','running','completed','failed','cancelled') NOT NULL DEFAULT 'queued',
    error_log TEXT DEFAULT NULL,
    started_at DATETIME DEFAULT NULL,
    completed_at DATETIME DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_ge_gj_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- GENERATION QUEUE (async batches)
-- ============================================================
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

-- ============================================================
-- CASE STUDIES
-- ============================================================
CREATE TABLE IF NOT EXISTS ge_case_studies (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL,
    service_id INT UNSIGNED DEFAULT NULL,
    category VARCHAR(100) DEFAULT NULL,
    client_name VARCHAR(255) DEFAULT NULL,
    client_industry VARCHAR(255) DEFAULT NULL,
    results_summary TEXT DEFAULT NULL,
    content LONGTEXT DEFAULT NULL,
    image VARCHAR(500) DEFAULT NULL,
    meta_title VARCHAR(255) DEFAULT NULL,
    meta_description TEXT DEFAULT NULL,
    faq_json JSON DEFAULT NULL,
    schema_json JSON DEFAULT NULL,
    status ENUM('published','draft') NOT NULL DEFAULT 'draft',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uk_ge_cs_slug (slug),
    INDEX idx_ge_cs_service (service_id),
    INDEX idx_ge_cs_status (status),
    CONSTRAINT fk_ge_cs_service FOREIGN KEY (service_id) REFERENCES ge_services(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- PILLAR PAGES
-- ============================================================
CREATE TABLE IF NOT EXISTS ge_pillar_pages (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL,
    silo VARCHAR(100) DEFAULT NULL,
    content LONGTEXT DEFAULT NULL,
    meta_title VARCHAR(255) DEFAULT NULL,
    meta_description TEXT DEFAULT NULL,
    linked_services JSON DEFAULT NULL,
    cluster_topics JSON DEFAULT NULL,
    status ENUM('published','draft') NOT NULL DEFAULT 'draft',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uk_ge_pp_slug (slug),
    INDEX idx_ge_pp_silo (silo)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- AUTHORS (EEAT)
-- ============================================================
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

-- ============================================================
-- REVIEWS
-- ============================================================
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

-- ============================================================
-- CRM
-- ============================================================
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

-- ============================================================
-- TOOLS MARKETPLACE
-- ============================================================
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

-- ============================================================
-- KNOWLEDGE BASE
-- ============================================================
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

-- ============================================================
-- COMPETITOR INTELLIGENCE
-- ============================================================
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

-- ============================================================
-- ANALYTICS
-- ============================================================
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

-- ============================================================
-- SETTINGS
-- ============================================================
CREATE TABLE IF NOT EXISTS ge_settings (
    setting_key VARCHAR(100) NOT NULL PRIMARY KEY,
    setting_value TEXT DEFAULT NULL,
    updated_at TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT IGNORE INTO ge_settings (setting_key, setting_value) VALUES
('url_pattern', '{url_prefix}-company-{city_slug}'),
('url_pattern_city', '{url_prefix}-company-in-{city_slug}'),
('url_pattern_industry', '{url_prefix}-company-in-{city_slug}-for-{industry_slug}'),
('founder_name', 'Ravindra Kumar Chauhan'),
('founder_title', 'Founder & CEO'),
('founder_experience', '5+ Years'),
('founder_linkedin', 'https://www.linkedin.com/in/ravindra-kumar-chauhan'),
('batch_size', '50'),
('auto_sitemap', '1'),
('auto_index_queue', '1'),
('platform_version', '2.0'),
('chatbot_enabled', '1'),
('chatbot_welcome', 'Hi! I am Nectra AI. How can I help you grow today?');

-- ============================================================
-- SEED DATA (admin can edit/delete anytime)
-- ============================================================
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

-- Import complete. Admin: https://www.nectradigital.com/admin/growth/
