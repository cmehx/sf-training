import os
import subprocess
from pathlib import Path
from datetime import datetime

# Load environment variables
#POSTGRES_USER = os.environ.get('POSTGRES_USER')
#POSTGRES_DB = os.environ.get('POSTGRES_DB')
POSTGRES_USER = "mehdi"
POSTGRES_DB   = "app"

if not all([POSTGRES_USER, POSTGRES_DB]):
    print("Error: environment variables are not set")
    exit(1)

# Define the path to the backup file
now = datetime.now()
backup_file_name = f"app-{now.strftime('%Y-%m-%d_%H-%M-%S')}.sql"
backup_file_path = Path('backups') / backup_file_name

# Use pg_dump to create the backup file
pg_dump_command = f"docker-compose exec database pg_dump -U {POSTGRES_USER} -d {POSTGRES_DB} > {backup_file_path}"
result = subprocess.run(pg_dump_command, shell=True)

# Check the result of the pg_dump command
if result.returncode != 0:
    print("Error: backup file could not be created")
    exit(1)

# Print the path to the backup file
print(f"Backup file created at {backup_file_path}")