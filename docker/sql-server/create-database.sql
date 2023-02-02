-- Create statement for the initial database on the SQL Server docker instance
IF NOT EXISTS
    (
        SELECT name FROM master.dbo.sysdatabases
        WHERE name = N'LocalDB'
    )
CREATE DATABASE LocalDB;
