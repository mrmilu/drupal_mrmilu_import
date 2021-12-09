# Mr. Milú import
Connection with Google Drive to import content

## Configuration

Set Google Drive params in system-settings:

```
$settings['drive_id'] = 'XXXXXX';
$settings['drive_auth_config'] = [path_to] . 'credentials.json';
$settings['drive_credentials'] = [path_to] . 'token.json';
```

## credentials.json

Configure Google Sheets API and download client_secret.json

## token.json

This file is generated dynamically (https://developers.google.com/sheets/api/quickstart/php).

In this project, go to **/admin/mrmilu/import** and click on markup link. This links redirects to something like:
http://project_path.com/?code=**XXXXXXXXXXXXXX**&scope=YYYYYY .

Copy XXXX and set in current form. If everything is ok, **token.json is configured** message will be shown.

## How it works

Create a php script, drupal path... and call it. In custom module write something like:

    $driveID = Settings::get('drive_id');
    $reader = new Reader();
    // 0: Sheet index
    // A2:E Range
    $values = $reader->getData($driveID, 0, 'A2:E');
