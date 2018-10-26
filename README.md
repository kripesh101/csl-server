# csl-server
A PHP and MySQL based server for CSL by xfl03

## Setup before running server

### 1. MySQL database
We need to create two tables.

- **Textures Table**

|Field Name|Type|Null|Key|
|:---:|:---:|:---:|:---:|
|TextureID|char(64)|NO|PRI|
|TextureData|varbinary(8191)|NO||

MySQL Command:

```sql
CREATE TABLE Textures(
  TextureID CHAR(64) NOT NULL,
  TextureData VARBINARY(8191) NOT NULL,
  PRIMARY KEY(TextureID) 
);
```

- **Users Table**

|Field Name|Type|Null|Key|
|:---:|:---:|:---:|:---:|
|UserName|varchar(16)|NO|PRI|
|UserKey|char(60)|NO||
|ModelID|char(64)|YES|MUL|
|CapeID|char(64)|YES|MUL|
|CurrentLoginToken|char(60)|YES||
|ModelType|char(1)|YES||

MySQL Command:

```sql
CREATE TABLE Users(
  UserName VARCHAR(16) NOT NULL,
  UserKey CHAR(60) NOT NULL,
  ModelID CHAR(64),
  CapeID CHAR(64),
  CurrentLoginToken CHAR(60),
  ModelType CHAR(1),
  PRIMARY KEY(UserName),
  FOREIGN KEY(ModelID) REFERENCES Textures(TextureID),
  FOREIGN KEY(CapeID) REFERENCES Textures(TextureID)
);
```

#### 2. Put the MySQL credentials into `sql-info.php` file.

#### 3. Done! Main Page for the server is index.php.
