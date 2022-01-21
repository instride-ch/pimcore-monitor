# Commands

### Health Check – `pimcore:monitor:health-check`

This command runs a complete health check of the system. It executes every registered health check
and prints its results in the console.

**Example output:**
```
Starting diagnostics:

F...!.....F..

2 failures, 1 warnings, 10 successful tests.                                    

Failure: App Environment
Application is not running in production mode

Warning: HTTPS Connection
HTTPS encryption could not be checked

Failure: Pimcore Maintenance
Pimcore maintenance is not activated
```

### Health Report – `pimcore:monitor:health-report`

This command collects the current health status and sends it to the defined API endpoint.
Behind the scenes it is sending the data using a PUT request with a Bearer token header
for authorization. Make sure to adapt your API endpoint to the specifics of this call.

#### Options

| Name      | Shortcut | Mode                          | Description                                                  |
|-----------|----------|-------------------------------|--------------------------------------------------------------|
| endpoint  |          | VALUE_REQUIRED                | Overwrite the default endpoint to send the report data to.   |
| exclude   | ex       | VALUE_OPTIONAL VALUE_IS_ARRAY | List any task alias that you want to exclude from execution. |
| include   | in       | VALUE_OPTIONAL VALUE_IS_ARRAY | List any task alias that you want to execute.                |
