<?php
// Script temporal para crear las credenciales en el servidor
$json_content = '{
  "type": "service_account",
  "project_id": "crm-stats-491409",
  "private_key_id": "122417ea926eed7c6eee6f22f29eab08114be346",
  "private_key": "-----BEGIN PRIVATE KEY-----\nMIIEvgIBADANBgkqhkiG9w0BAQEFAASCBKgwggSkAgEAAoIBAQCkGQZOfcGsFK3P\neXdE2VIbaHKfMFs1+32tEQ1i7pO4c/awNRt6YzSMi+sp0/6Z3F0cPymZyfkVObvc\nMjAyuQ0q0wZqlfdnGqoA9a+dDwueNksCH1CeUALE7jnYOz2oi3xj6sVt+J8SAeTP\n3WEbomibyg9jXm6yhIpCHN4BkP5fHqwTN0/w/37kQ8wovVEn0fPMck/F2bJaxgYh\nFRFHqodeYEwZkPrMJca/3U81rOkYOySiQdfc/zUdw5bOVUNIqS7CKtw2ZsuxKxwf\nBW3tdUguhJqqWsT3HcgOn6zS4h/XWyao0mpYl/YYEv5/lRMQgle9RX2/4Pg36nSE\nWcA7Qw3JAgMBAAECggEAJouCQbmkXmlVEmDztQZfFPUJx36yThHOyXIx9MUZicAj\nd+xhkfPvifvrtx2GtllevZ3QhcE9ctDm4aggTinNf/uljC/Bt0Q6HpOXNossLXOP\nVuZIJ4efvuY+TB5OM2LgSxAAY/58TtyHBJR5lOYnMEDcMHkkA2bCmxjwxOlr9qd7\naKrInUl6xz6IhBWVMFOhsMD82UCMZfQiQb4fPCeHZplo6T7SKd74M2FIq2VWOR5a\nvNFrEcAH9psVHOFGK+2taMhI4krn6HTvYCUuW8dvqSTiksxxo/n8lJuxTTaIQQFg\nJkD+OBkQdrTpWUgI9gHfVmKDCVIWSTQqypDy/V5ZoQKBgQDT16phBctbo6oIkyPC\nP6iAyVdxeWEsC++JIzBRlTFRiiCJxTVkD+nKmB91DnRG9EjbYPf0LY/Lsvj3RqeT\nlCxb1adPdz47AqQuWDWwyQyf7lwIFDzobWx4uvPlXCeT0z8Nv1fWlKB38VLY6sYc\nsejnrd/YbCHUmVwpPruoBn6HdQKBgQDGTZt6ztThfGE5+oqae5asgwKx9aPEoNjy\nKvRDzNXyHDWoApwfWsw7LTRsFeSPJDx1K+7tC4vahdj2eLSFNl6JC+IDTtgKQa/w\n2g9Bqox0OQgmKj0NQaABbDZOrsYp11CpZfvzGlqp9U3EBv/RHjHYOawzBCu0k7Sk\ftyhVKQ2hQKBgQCKJzfiFllM5itZdDv5znIhqyzvv+zzQMVydhRo4BdNyfr2Xjo3\ni3k4uoSpXkfOjjmFeHO8Nop0mb9r5BQdVqCnM2rtlCJtcYeJAdXh+PvWwSgbzLb8\nDYCUK7greDIHYdjZKlQqiLst/Z+9/z48MHpnvSkih0J415sW+uZ+6d1SkQKBgDVF\nyA0X32ysA1qWCgIyTWoOz/yK5vH93ApCq9hDAP7HwUpfWR27QL5tCHY482vY6jz4\nDnmz7mBIMN8V0jkvP/pFLPSCi9tFhkSH8C1N6emXIK9QDZKwHJIFRhmhLi8zXsID\nWcoXPGAIPFn7H2JIYE+cPrx91Ffwjkjao97bZDoZAoGBAKGyR8EO8ueExprO7QIt\nLN1hgX6BviF07M+4CaNHfB9xb/s27YB5a0w8UYyfGRIBE3INlinASGJ5pL3MY3nj\neRE4cBukv8OpPIC3mTYm2WIr8ay72APtf+8qKOTYne5DICqLogfNLhb0guE26x6G\nxH92r9apjstXrEoCEph80c/q\n-----END PRIVATE KEY-----\n",
  "client_email": "crm-analytics@crm-stats-491409.iam.gserviceaccount.com",
  "client_id": "104259808024146726925",
  "auth_uri": "https://accounts.google.com/o/oauth2/auth",
  "token_uri": "https://oauth2.googleapis.com/token",
  "auth_provider_x509_cert_url": "https://www.googleapis.com/api/oauth2/v1/certs",
  "client_x509_cert_url": "https://www.googleapis.com/robot/v1/metadata/x509/crm-analytics%40crm-stats-491409.iam.gserviceaccount.com",
  "universe_domain": "googleapis.com"
}';

$file_path = __DIR__ . '/google-credentials.json';

if (file_put_contents($file_path, $json_content)) {
    echo "<h1>✅ Archivo creado con éxito en el servidor</h1>";
    echo "<p>Ubicación: $file_path</p>";
    echo "<p>Ya puedes volver al Dashboard de Estadísticas.</p>";
} else {
    echo "<h1>❌ Error al crear el archivo</h1>";
    echo "<p>Comprueba los permisos de escritura de la carpeta.</p>";
}
