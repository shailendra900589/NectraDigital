-- Nectra Digital Growth Engine — Database Schema
-- Scalable for 10,000+ cities, 1,000+ services, 100,000+ landing pages

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
-- KEYWORDS
-- ============================================================
CREATE TABLE IF NOT EXISTS ge_keywords (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    keyword VARCHAR(500) NOT NULL,
    keyword_type ENUM('primary','secondary','lsi','commercial','transactional','informational','local') NOT NULL DEFAULT 'primary',
    service_id INT UNSIGNED DEFAULT NULL,
    city_id INT UNSIGNED DEFAULT NULL,
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
-- LANDING PAGES (programmatic SEO)
-- ============================================================
CREATE TABLE IF NOT EXISTS ge_landing_pages (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    service_id INT UNSIGNED NOT NULL,
    city_id INT UNSIGNED NOT NULL,
    slug VARCHAR(500) NOT NULL,
    url_path VARCHAR(500) NOT NULL,
    meta_title VARCHAR(255) DEFAULT NULL,
    meta_description TEXT DEFAULT NULL,
    h1 VARCHAR(500) DEFAULT NULL,
    h2 VARCHAR(500) DEFAULT NULL,
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
    content_hash CHAR(64) DEFAULT NULL,
    is_indexed TINYINT(1) NOT NULL DEFAULT 0,
    index_status ENUM('pending','submitted','indexed','excluded','failed') NOT NULL DEFAULT 'pending',
    index_submitted_at DATETIME DEFAULT NULL,
    index_verified_at DATETIME DEFAULT NULL,
    status ENUM('published','draft','archived') NOT NULL DEFAULT 'published',
    generated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uk_ge_lp_slug (slug),
    UNIQUE KEY uk_ge_lp_service_city (service_id, city_id),
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
-- PILLAR PAGES (blog authority)
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
-- SETTINGS
-- ============================================================
CREATE TABLE IF NOT EXISTS ge_settings (
    setting_key VARCHAR(100) NOT NULL PRIMARY KEY,
    setting_value TEXT DEFAULT NULL,
    updated_at TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT IGNORE INTO ge_settings (setting_key, setting_value) VALUES
('url_pattern', '{url_prefix}-company-{city_slug}'),
('founder_name', 'Ravindra Kumar Chauhan'),
('founder_title', 'Founder & CEO'),
('founder_experience', '5+ Years'),
('founder_linkedin', 'https://www.linkedin.com/in/ravindra-kumar-chauhan'),
('batch_size', '50'),
('auto_sitemap', '1'),
('auto_index_queue', '1');

SET FOREIGN_KEY_CHECKS = 1;
