<html>
<head>
    <script src="http://static.opentok.com/webrtc/v2.0/js/TB.min.js" ></script>
</head>

<body>
    <script type="text/javascript">
  var apiKey    = "44699972";
  var sessionId = "2_MX40NDY5OTk3Mn5-V2VkIE1hciAxOSAxOTo0OTozNSBQRFQgMjAxNH4wLjUwODA2Njd-";
  var token     = "T1==cGFydG5lcl9pZD00NDY5OTk3MiZzZGtfdmVyc2lvbj10YnJ1YnktdGJyYi12MC45MS4yMDExLTAyLTE3JnNpZz02OWE5ZWI0MjZkNWVjNDU4OGZlMmFiNDE2ZDE5ZTliYTY4MzgzOGM3OnJvbGU9cHVibGlzaGVyJnNlc3Npb25faWQ9Ml9NWDQwTkRZNU9UazNNbjUtVjJWa0lFMWhjaUF4T1NBeE9UbzBPVG96TlNCUVJGUWdNakF4Tkg0d0xqVXdPREEyTmpkLSZjcmVhdGVfdGltZT0xMzk1MjgzNzg5Jm5vbmNlPTAuODMyNjAwMDk4MjU5NTg5JmV4cGlyZV90aW1lPTEzOTUzNzAxNDcmY29ubmVjdGlvbl9kYXRhPQ==";
 
  function sessionConnectedHandler (event) {
     session.publish( publisher );
     subscribeToStreams(event.streams);
  }
  function subscribeToStreams(streams) {
    for (var i = 0; i < streams.length; i++) {
        var stream = streams[i];
        if (stream.connection.connectionId 
               != session.connection.connectionId) {
            session.subscribe(stream);
        }
    }
  }
  function streamCreatedHandler(event) {
    subscribeToStreams(event.streams);
  }
 
  var publisher = TB.initPublisher(apiKey);
  var session   = TB.initSession(sessionId);
 
  session.connect(apiKey, token);
  session.addEventListener("sessionConnected", 
                           sessionConnectedHandler);
 
  session.addEventListener("streamCreated", 
                           streamCreatedHandler);
</script>
</body>
</html>