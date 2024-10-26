import mysql.connector
from datetime import datetime

# Database configuration
dbconfig = {
    'host': '172.31.19.243',
    'port': 3306,
    'user': 'tpglobalfx_u_test',
    'password': '7#Dq5@^dJC8$(&A1',
    'database': 'cloud_tpglobalfx'
}

# Set up file path for SQL dump
date = datetime.now().strftime('%Y-%m-%d_%H-%M-%S')
backup_file = f"{dbconfig['database']}_backup_{date}.sql"

try:
    # Connect to the database
    conn = mysql.connector.connect(**dbconfig)
    cursor = conn.cursor()

    # Open file to write the backup
    with open(backup_file, 'w') as f:
        f.write(f"-- Database backup for `{dbconfig['database']}`\n")
        f.write(f"-- Generated: {datetime.now()}\n\n")

        # Get all tables in the database
        cursor.execute("SHOW TABLES")
        tables = cursor.fetchall()

        for (table,) in tables:
            # Write table structure
            cursor.execute(f"SHOW CREATE TABLE `{table}`")
            create_table_stmt = cursor.fetchone()[1]
            f.write(f"\n\n-- Table structure for table `{table}`\n")
            f.write(f"{create_table_stmt};\n")

            # Dump the data for each table
            f.write(f"\n-- Dumping data for table `{table}`\n")
            cursor.execute(f"SELECT * FROM `{table}`")
            rows = cursor.fetchall()

            # Write each row as an INSERT statement
            for row in rows:
                values = ', '.join([f"'{str(val).replace("'", "''")}'" if val is not None else 'NULL' for val in row])
                f.write(f"INSERT INTO `{table}` VALUES ({values});\n")

    print(f"Database backup successful! File saved as: {backup_file}")

except mysql.connector.Error as err:
    print(f"Error creating database backup: {err}")

finally:
    cursor.close()
    conn.close()
