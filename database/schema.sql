-- MySQL 5.7.8+ or MariaDB 10.4+. Select your hosting database before import.
-- All timestamps are UTC. Appointment date/time use the stored IANA timezone.
CREATE TABLE IF NOT EXISTS demo_requests (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    service_slug VARCHAR(80) NOT NULL,
    name VARCHAR(120) NOT NULL,
    email VARCHAR(254) NOT NULL,
    phone VARCHAR(40) NOT NULL,
    company VARCHAR(160) NOT NULL,
    job_title VARCHAR(120) NOT NULL DEFAULT '',
    preferred_date DATE NOT NULL,
    preferred_time TIME NOT NULL,
    timezone VARCHAR(64) NOT NULL DEFAULT 'Asia/Kolkata',
    mode VARCHAR(30) NOT NULL,
    extra_data JSON NOT NULL,
    message TEXT NOT NULL,
    status ENUM('new', 'contacted', 'closed') NOT NULL DEFAULT 'new',
    internal_email_status ENUM('pending', 'sent', 'failed') NOT NULL DEFAULT 'pending',
    confirmation_email_status ENUM('pending', 'sent', 'failed') NOT NULL DEFAULT 'pending',
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_demo_status_created (status, created_at),
    KEY idx_demo_service_created (service_slug, created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS contact_requests (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    name VARCHAR(120) NOT NULL,
    email VARCHAR(254) NOT NULL,
    phone VARCHAR(40) NOT NULL DEFAULT '',
    subject VARCHAR(200) NOT NULL,
    message TEXT NOT NULL,
    status ENUM('new', 'contacted', 'closed') NOT NULL DEFAULT 'new',
    internal_email_status ENUM('pending', 'sent', 'failed') NOT NULL DEFAULT 'pending',
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_contact_status_created (status, created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Optional enquiry, case-study and consultation features
-- Additive feature migration: safe to import alongside an existing schema.
CREATE TABLE IF NOT EXISTS enquiry_notes (
 id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
 request_type VARCHAR(20) NOT NULL, request_id BIGINT UNSIGNED NOT NULL,
 note TEXT NOT NULL, created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
 KEY idx_enquiry_notes (request_type,request_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE IF NOT EXISTS enquiry_briefs (
 contact_id BIGINT UNSIGNED NOT NULL PRIMARY KEY, brief_json JSON NOT NULL,
 CONSTRAINT fk_brief_contact FOREIGN KEY (contact_id) REFERENCES contact_requests(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE IF NOT EXISTS consultation_slots (
 id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
 starts_at DATETIME NOT NULL, status VARCHAR(20) NOT NULL DEFAULT 'available',
 created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
 UNIQUE KEY uq_consultation_start (starts_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE IF NOT EXISTS consultation_bookings (
 id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
 slot_id BIGINT UNSIGNED NOT NULL, name VARCHAR(120) NOT NULL,
 email VARCHAR(254) NOT NULL, phone VARCHAR(40) NOT NULL DEFAULT '',
 message TEXT NOT NULL, status VARCHAR(20) NOT NULL DEFAULT 'confirmed',
 internal_email_status VARCHAR(20) NOT NULL DEFAULT 'pending',
 confirmation_email_status VARCHAR(20) NOT NULL DEFAULT 'pending',
 created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
 UNIQUE KEY uq_consultation_booking_slot (slot_id),
 CONSTRAINT fk_booking_slot FOREIGN KEY (slot_id) REFERENCES consultation_slots(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE IF NOT EXISTS case_studies (
 id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
 slug VARCHAR(100) NOT NULL, title VARCHAR(180) NOT NULL,
 summary VARCHAR(350) NOT NULL, client_name VARCHAR(160) NOT NULL DEFAULT '',
 challenge TEXT NOT NULL, solution TEXT NOT NULL, outcome TEXT NOT NULL,
 project_url VARCHAR(1000) NOT NULL DEFAULT '', image_path VARCHAR(255) NOT NULL DEFAULT '',
 image_alt VARCHAR(250) NOT NULL DEFAULT '', status VARCHAR(20) NOT NULL DEFAULT 'draft',
 evidence_approved TINYINT NOT NULL DEFAULT 0,
 created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
 updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
 UNIQUE KEY uq_case_slug (slug)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
