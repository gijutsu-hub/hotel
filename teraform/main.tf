# Terraform Configuration for IAM User and DynamoDB Table

provider "aws" {
  region = "us-east-1"
}

# Create IAM User "joseph"
resource "aws_iam_user" "joseph" {
  name = "joseph"
}

# Attach Policy to User "joseph"
resource "aws_iam_user_policy" "joseph_dynamodb_policy" {
  name   = "DynamoDBAccessPolicy"
  user   = aws_iam_user.joseph.name
  policy = jsonencode({
    Version = "2012-10-17",
    Statement = [
      {
        Effect   = "Allow",
        Action   = [
          "dynamodb:ListTables",
          "dynamodb:DescribeTable",
          "dynamodb:Query",
          "dynamodb:Scan",
          "dynamodb:UpdateItem",
          "dynamodb:DeleteItem"
        ],
        Resource = "*"
      }
    ]
  })
}

# Generate Access Keys for User "joseph"
resource "aws_iam_access_key" "joseph_access_key" {
  user = aws_iam_user.joseph.name
}

output "access_key_id" {
  value = aws_iam_access_key.joseph_access_key.id
}

output "secret_access_key" {
  value     = aws_iam_access_key.joseph_access_key.secret
  sensitive = true
}

# Create DynamoDB Table "AccountsTable"
resource "aws_dynamodb_table" "accounts_table" {
  name           = "AccountsTable"
  billing_mode   = "PROVISIONED"
  read_capacity  = 5
  write_capacity = 5

  hash_key = "email"

  attribute {
    name = "email"
    type = "S"
  }
}
