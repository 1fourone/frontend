# CS490 Project - Frontend

The frontend to our (unnamed as of right now) CS490 project.

### Architecture
![architecture](490_architecture.png "Architecture")


### Data
Will send a `Credentials` JSON object to Middle.
```json
{
    "user": user_name,
    "password": their_hashed_password
}
```

Expects a `Result` JSON object from Middle.
```json 
{
    "njit" : "success",
    "local" : "failure"
}
```