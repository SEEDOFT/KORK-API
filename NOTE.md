-- Users table
CREATE TABLE user (
  id INT PRIMARY KEY,
  email VARCHAR(150) UNIQUE NOT NULL,
  phone_number VARCHAR(20) NULL,
  password VARCHAR(255) NOT NULL,
  created_at DATETIME,
  updated_at DATETIME
);

create table profile(
  id int primary key,
  user_id int,
)

-- Event table
CREATE TABLE event (
  id INT PRIMARY KEY,
  user_id INT NOT NULL,
  event_type varchar(50) Not null,
  event_name VARCHAR(100) NOT NULL,
  location VARCHAR(255) NULL,
  start_time DATETIME NULL,
  end_time DATETIME NULL,
  description TEXT NULL,
  organizer_name VARCHAR(100) NULL,
  poster_url VARCHAR(255) NULL,
  created_at DATETIME,
  updated_at DATETIME,
  CONSTRAINT fk_event_user FOREIGN KEY(user_id) REFERENCES user(id),
  CONSTRAINT fk_event_event_type FOREIGN KEY(event_type_id) REFERENCES event_type(id)
);

-- Ticket Types table
CREATE TABLE ticket_type (
  id INT PRIMARY KEY,
  event_id int
  ticket_type VARCHAR(100) NOT NULL,
  available_ticket unsignedinteger,
  price,
  created_at DATETIME,
  updated_at DATETIME,
  CONSTRAINT fk_ticket_types_event_type FOREIGN KEY(event_type_id) REFERENCES event_type(id)
);

-- Ticket table issues a unique ticket for each purchased ticket.
CREATE TABLE ticket (
  id int PRIMARY KEY,
  event_id int,
  ticket_type_id int,
  user_id int,
  ticket_code VARCHAR(100) UNIQUE NOT NULL,
  amount_paid,
  payment_confirmed boolean,
  created_at DATETIME,
  updated_at DATETIME,
  CONSTRAINT fk_ticket_buy_ticket FOREIGN KEY(buy_ticket_id) REFERENCES buy_ticket(id),
  CONSTRAINT fk_ticket_event_ticket FOREIGN KEY(event_ticket_id) REFERENCES event_ticket(id)
);

create table ticket_verification(
id int primary key,
ticket_id int,
user_id,
payment_confirmed boolean,
created_at DATETIME,
updated_at DATETIME,
);

CREATE TABLE bookmark (
  id INT PRIMARY KEY,
  user_id INT NOT NULL,
  event_id INT NOT NULL,
  created_at DATETIME,
  deleted_at DATETIME,
  CONSTRAINT fk_bookmark_user FOREIGN KEY(user_id) REFERENCES user(id),
  CONSTRAINT fk_bookmark_event FOREIGN KEY(event_id) REFERENCES event(id),
  CONSTRAINT unique_user_event UNIQUE(user_id, event_id)
);

-- Credit Card table
CREATE TABLE credit_card (
  id INT PRIMARY KEY,
  user_id INT NOT NULL,
  card_holder_name VARCHAR(100) NOT NULL,
  card_number VARCHAR(16) NOT NULL,  -- Store only the last 4 digits for security
  card_hash VARCHAR(255) NOT NULL,     -- Encrypted full card number
  expiration_date CHAR(5) NOT NULL,      -- Format: MM/YY
  card_type VARCHAR(20) CHECK (card_type IN ('Visa', 'MasterCard', 'Amex', 'Discover')),
  created_at DATETIME,
  updated_at DATETIME,
  CONSTRAINT fk_credit_cards_user FOREIGN KEY(user_id) REFERENCES user(id),
  CONSTRAINT unique_card UNIQUE(user_id, card_hash)
);

CREATE TABLE profiles (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL UNIQUE,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    gender VARCHAR(10) NULL,
    dob DATE NULL,
    profile_image VARCHAR(255) NULL,
    nationality VARCHAR(50) NULL,
    location VARCHAR(255) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);


Table	Relationship
profiles → users	user_id (One-to-One)
events → users	user_id (One-to-Many)
events → event_types	event_type_id (One-to-Many)
event_tickets → events	event_id (One-to-Many)
event_tickets → ticket_types	ticket_type_id (One-to-Many)
bookmarks → users	user_id (Many-to-One)
bookmarks → events	event_id (Many-to-One)
buy_tickets → users	user_id (One-to-Many)
buy_tickets → events	event_id (One-to-Many)
tickets → buy_tickets	buy_ticket_id (Many-to-One)
tickets → event_tickets	event_ticket_id (Many-to-One)
credit_cards → users	user_id (One-to-Many)