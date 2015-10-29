<script>
  var clientId = '190045122219-a4cve4oo8jhad4ot0uos3mrmfqm53nkr.apps.googleusercontent.com';
  var developerKey = 'LxyPhtxeYhgGsVFbC5EKk1yu';
  var accessToken;
  
  function onApiLoad() {
    gapi.load('auth', authenticateWithGoogle);
    gapi.load('picker');
  }
  
  function authenticateWithGoogle() {
    window.gapi.auth.authorize({
      'client_id': clientId,
      'scope': ['https://www.googleapis.com/auth/drive.readonly']
    }, handleAuthentication);
  }
  
  function handleAuthentication(result) {
      
      //console.log(result);
    if(result && !result.error) {
      accessToken = result.access_token;
      setupPicker();
    }
  }
  
  function setupPicker() {
    var picker = new google.picker.PickerBuilder()
    .setOAuthToken(accessToken)
    //.setDeveloperKey(developerKey)
    //.setAppId(clientId)
    .addView(new google.picker.DocsUploadView())
    .addView(new google.picker.DocsView())
    .setCallback(pickerCallback)
    .enableFeature(google.picker.Feature.NAV_HIDDEN)
    .build();
    .setVisible(true);
  }
  
  function pickerCallback(data){
      if(data.action == google.picker.Action.PICKED){
          console.log(data);
          alert('URL: ' + data.docs[0].url);
      }
  }
</script>
<script src="https://apis.google.com/js/api.js?onload=onApiLoad"></script>