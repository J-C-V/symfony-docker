# Run the setup script to create the database
# Do this in a loop because the timing for when the SQL instance is ready is indeterminate
#
# Note: Make sure that your password matches what is in the .env
for i in {1..60};
do
    /opt/mssql-tools/bin/sqlcmd -S localhost -U sa -P Strong_Password -i create-database.sql
    if [ $? -eq 0 ]
    then
        echo "Database initialization complete."
        break
    else
        echo "SQL Server is not ready yet..."
        sleep 1
    fi
done
