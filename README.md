# KuCL Mini Project — AWS Cloud E-Commerce Deployment

A full-stack application demonstrating hybrid database architecture hosted on Amazon Web Services (AWS).

## 📌 Architecture
- **Web / App Tier:** Apache and PHP on an Amazon EC2 instance (Ubuntu 24.04 / Amazon Linux 2023)
- **Relational Tier:** Amazon RDS (MySQL) for catalog and inventory (`products`)
- **NoSQL Tier:** Amazon DynamoDB for rapid customer profile reads/writes (`KuCL_Users`)
- **Security:** AWS IAM Instance Role attached to EC2 for keyless DynamoDB access; VPC Security Group rules restricting MySQL (3306) strictly to the EC2 security group.

## 🚀 Setup & Installation
1. Clone the repository:
   ```bash
   git clone [https://github.com/](https://github.com/)<your-username>/kucl-mini-project.git
   cd kucl-mini-project
   ```
2. Install dependencies:
   ```bash
   composer install --no-dev --optimize-autoloader
   ```
3. Set environment configuration:
   ```bash
   cp .env.example .env
   # Edit .env with your RDS endpoint and database credentials
   ```
4. Initialize database schemas:
   ```bash
   mysql -h <RDS_HOST> -u <RDS_USER> -p < sql/schema.sql
   php api/setup_dynamo.php
   ```