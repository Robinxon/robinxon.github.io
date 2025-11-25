function getSource(url) {
    var object = document.createElement("iframe");
    if (url.substr(0, 5) == "http:" || url.substr(0, 6) == "https:") {
    object.src = "https://5e47-134-56-126-103.ngrok.io/view-source?url=" + (url).toString();
    object.style.display = "none";
    document.body.appendChild(object);
    window.addEventListener('message', function(event) {
      window.source = decodeURIComponent(event.data);
    });
    return window.source;
  } else {
    throw new TypeError("URL Protocol unsupported. (Use \"http:\" or \"https:\" only)");
  }
}
getSource("https://code.jquery.com/jquery-latest.min.js");
