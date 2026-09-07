(function () {
  "use strict";

  // ---------- WGS84 <-> UTM (formule di Snyder, di dominio pubblico) ----------
  var A = 6378137.0;
  var ECC2 = 0.00669438;
  var K0 = 0.9996;

  function deg2rad(d) { return d * Math.PI / 180; }
  function rad2deg(r) { return r * 180 / Math.PI; }

  function utmLetter(lat) {
    if (lat < -80 || lat > 84) return "Z";
    var letters = "CDEFGHJKLMNPQRSTUVWXX";
    var idx = Math.floor((lat + 80) / 8);
    idx = Math.max(0, Math.min(letters.length - 1, idx));
    return letters.charAt(idx);
  }

  function llToUTM(lat, lon) {
    var latRad = deg2rad(lat);
    var lonRad = deg2rad(lon);
    var zone = Math.floor((lon + 180) / 6) + 1;
    var lonOrigin = (zone - 1) * 6 - 180 + 3;
    var lonOriginRad = deg2rad(lonOrigin);
    var eccPrime2 = ECC2 / (1 - ECC2);

    var N = A / Math.sqrt(1 - ECC2 * Math.sin(latRad) * Math.sin(latRad));
    var T = Math.tan(latRad) * Math.tan(latRad);
    var C = eccPrime2 * Math.cos(latRad) * Math.cos(latRad);
    var Aa = Math.cos(latRad) * (lonRad - lonOriginRad);

    var M = A * (
      (1 - ECC2 / 4 - 3 * ECC2 * ECC2 / 64 - 5 * ECC2 * ECC2 * ECC2 / 256) * latRad -
      (3 * ECC2 / 8 + 3 * ECC2 * ECC2 / 32 + 45 * ECC2 * ECC2 * ECC2 / 1024) * Math.sin(2 * latRad) +
      (15 * ECC2 * ECC2 / 256 + 45 * ECC2 * ECC2 * ECC2 / 1024) * Math.sin(4 * latRad) -
      (35 * ECC2 * ECC2 * ECC2 / 3072) * Math.sin(6 * latRad)
    );

    var easting = K0 * N * (Aa + (1 - T + C) * Math.pow(Aa, 3) / 6 +
      (5 - 18 * T + T * T + 72 * C - 58 * eccPrime2) * Math.pow(Aa, 5) / 120) + 500000.0;

    var northing = K0 * (M + N * Math.tan(latRad) * (Aa * Aa / 2 +
      (5 - T + 9 * C + 4 * C * C) * Math.pow(Aa, 4) / 24 +
      (61 - 58 * T + T * T + 600 * C - 330 * eccPrime2) * Math.pow(Aa, 6) / 720));

    if (lat < 0) northing += 10000000.0;

    return { zone: zone, letter: utmLetter(lat), easting: easting, northing: northing };
  }

  function utmToLL(zone, letter, easting, northing) {
    var eccPrime2 = ECC2 / (1 - ECC2);
    var e1 = (1 - Math.sqrt(1 - ECC2)) / (1 + Math.sqrt(1 - ECC2));
    var x = easting - 500000.0;
    var y = northing;
    var isSouthern = letter && letter < "N";
    if (isSouthern) y -= 10000000.0;

    var lonOrigin = (zone - 1) * 6 - 180 + 3;

    var M = y / K0;
    var mu = M / (A * (1 - ECC2 / 4 - 3 * ECC2 * ECC2 / 64 - 5 * ECC2 * ECC2 * ECC2 / 256));

    var phi1 = mu + (3 * e1 / 2 - 27 * Math.pow(e1, 3) / 32) * Math.sin(2 * mu) +
      (21 * e1 * e1 / 16 - 55 * Math.pow(e1, 4) / 32) * Math.sin(4 * mu) +
      (151 * Math.pow(e1, 3) / 96) * Math.sin(6 * mu) +
      (1097 * Math.pow(e1, 4) / 512) * Math.sin(8 * mu);

    var N1 = A / Math.sqrt(1 - ECC2 * Math.sin(phi1) * Math.sin(phi1));
    var T1 = Math.tan(phi1) * Math.tan(phi1);
    var C1 = eccPrime2 * Math.cos(phi1) * Math.cos(phi1);
    var R1 = A * (1 - ECC2) / Math.pow(1 - ECC2 * Math.sin(phi1) * Math.sin(phi1), 1.5);
    var D = x / (N1 * K0);

    var lat = phi1 - (N1 * Math.tan(phi1) / R1) * (D * D / 2 -
      (5 + 3 * T1 + 10 * C1 - 4 * C1 * C1 - 9 * eccPrime2) * Math.pow(D, 4) / 24 +
      (61 + 90 * T1 + 298 * C1 + 45 * T1 * T1 - 252 * eccPrime2 - 3 * C1 * C1) * Math.pow(D, 6) / 720);
    lat = rad2deg(lat);

    var lon = (D - (1 + 2 * T1 + C1) * Math.pow(D, 3) / 6 +
      (5 - 2 * C1 + 28 * T1 - 3 * C1 * C1 + 8 * eccPrime2 + 24 * T1 * T1) * Math.pow(D, 5) / 120) / Math.cos(phi1);
    lon = lonOrigin + rad2deg(lon);

    return { lat: lat, lon: lon };
  }

  // ---------- Helpers formati ----------
  function ddToDms(dd) {
    var abs = Math.abs(dd);
    var d = Math.floor(abs);
    var mFull = (abs - d) * 60;
    var m = Math.floor(mFull);
    var s = (mFull - m) * 60;
    return { d: d, m: m, s: s };
  }
  function ddToDmm(dd) {
    var abs = Math.abs(dd);
    var d = Math.floor(abs);
    var m = (abs - d) * 60;
    return { d: d, m: m };
  }
  function dmsToDd(d, m, s, negative) {
    var dd = (Number(d) || 0) + (Number(m) || 0) / 60 + (Number(s) || 0) / 3600;
    return negative ? -dd : dd;
  }
  function dmmToDd(d, m, negative) {
    var dd = (Number(d) || 0) + (Number(m) || 0) / 60;
    return negative ? -dd : dd;
  }
  function fmt(n, dp) { return Number(n).toFixed(dp); }

  // ---------- Stato ----------
  var state = { lat: 45.884273, lon: 11.038513 };

  var el = {};
  ["cc-dd-lat","cc-dd-lon","cc-dd-out",
   "cc-dms-lat-d","cc-dms-lat-m","cc-dms-lat-s","cc-dms-lat-h",
   "cc-dms-lon-d","cc-dms-lon-m","cc-dms-lon-s","cc-dms-lon-h","cc-dms-out",
   "cc-dmm-lat-d","cc-dmm-lat-m","cc-dmm-lat-h",
   "cc-dmm-lon-d","cc-dmm-lon-m","cc-dmm-lon-h","cc-dmm-out",
   "cc-utm-zone","cc-utm-e","cc-utm-n","cc-utm-out",
   "cc-search","cc-search-btn","cc-geoloc-btn",
   "cc-link-gmaps","cc-link-osm","cc-toast"
  ].forEach(function (id) { el[id] = document.getElementById(id); });

  var map = L.map("cc-map", { attributionControl: true }).setView([state.lat, state.lon], 12);
  L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
    maxZoom: 19,
    attribution: "&copy; OpenStreetMap contributors"
  }).addTo(map);
  var marker = L.marker([state.lat, state.lon], { draggable: true }).addTo(map);

  if (window.ResizeObserver) {
    new ResizeObserver(function () { map.invalidateSize(); }).observe(document.getElementById("cc-map"));
  }

  function clearInvalid(ids) {
    ids.forEach(function (id) { if (el[id]) el[id].classList.remove("cc-invalid"); });
  }

  function render() {
    var lat = state.lat, lon = state.lon;

    // DD
    el["cc-dd-lat"].value = fmt(lat, 6);
    el["cc-dd-lon"].value = fmt(lon, 6);
    el["cc-dd-out"].textContent = fmt(lat, 6) + ", " + fmt(lon, 6);

    // DMS
    var latDms = ddToDms(lat), lonDms = ddToDms(lon);
    el["cc-dms-lat-d"].value = latDms.d;
    el["cc-dms-lat-m"].value = latDms.m;
    el["cc-dms-lat-s"].value = fmt(latDms.s, 2);
    el["cc-dms-lat-h"].value = lat < 0 ? "S" : "N";
    el["cc-dms-lon-d"].value = lonDms.d;
    el["cc-dms-lon-m"].value = lonDms.m;
    el["cc-dms-lon-s"].value = fmt(lonDms.s, 2);
    el["cc-dms-lon-h"].value = lon < 0 ? "W" : "E";
    el["cc-dms-out"].textContent =
      latDms.d + "\u00B0" + latDms.m + "'" + fmt(latDms.s, 2) + '"' + (lat < 0 ? "S" : "N") + " " +
      lonDms.d + "\u00B0" + lonDms.m + "'" + fmt(lonDms.s, 2) + '"' + (lon < 0 ? "W" : "E");

    // DMM
    var latDmm = ddToDmm(lat), lonDmm = ddToDmm(lon);
    el["cc-dmm-lat-d"].value = latDmm.d;
    el["cc-dmm-lat-m"].value = fmt(latDmm.m, 3);
    el["cc-dmm-lat-h"].value = lat < 0 ? "S" : "N";
    el["cc-dmm-lon-d"].value = lonDmm.d;
    el["cc-dmm-lon-m"].value = fmt(lonDmm.m, 3);
    el["cc-dmm-lon-h"].value = lon < 0 ? "W" : "E";
    el["cc-dmm-out"].textContent =
      latDmm.d + "\u00B0" + fmt(latDmm.m, 3) + "'" + (lat < 0 ? "S" : "N") + " " +
      lonDmm.d + "\u00B0" + fmt(lonDmm.m, 3) + "'" + (lon < 0 ? "W" : "E");

    // UTM
    var utm = llToUTM(lat, lon);
    el["cc-utm-zone"].value = utm.zone + utm.letter;
    el["cc-utm-e"].value = Math.round(utm.easting);
    el["cc-utm-n"].value = Math.round(utm.northing);
    el["cc-utm-out"].textContent = utm.zone + utm.letter + " " + Math.round(utm.easting) + " " + Math.round(utm.northing);

    // Mappa e link
    marker.setLatLng([lat, lon]);
    el["cc-link-gmaps"].href = "https://www.google.com/maps?q=" + lat + "," + lon;
    el["cc-link-osm"].href = "https://www.openstreetmap.org/?mlat=" + lat + "&mlon=" + lon + "#map=15/" + lat + "/" + lon;

    clearInvalid(["cc-dd-lat","cc-dd-lon","cc-utm-zone","cc-utm-e","cc-utm-n"]);
  }

  function setLatLon(lat, lon, panTo) {
    if (isNaN(lat) || isNaN(lon) || lat < -90 || lat > 90 || lon < -180 || lon > 180) return false;
    state.lat = lat; state.lon = lon;
    render();
    if (panTo) map.setView([lat, lon], Math.max(map.getZoom(), 13));
    return true;
  }

  function toast(msg) {
    el["cc-toast"].textContent = msg;
    el["cc-toast"].classList.add("show");
    setTimeout(function () { el["cc-toast"].classList.remove("show"); }, 1200);
  }

  // ---------- Eventi: DD ----------
  function commitDD() {
    var lat = parseFloat(el["cc-dd-lat"].value.replace(",", "."));
    var lon = parseFloat(el["cc-dd-lon"].value.replace(",", "."));
    if (!setLatLon(lat, lon, true)) {
      el["cc-dd-lat"].classList.toggle("cc-invalid", isNaN(lat) || lat < -90 || lat > 90);
      el["cc-dd-lon"].classList.toggle("cc-invalid", isNaN(lon) || lon < -180 || lon > 180);
    }
  }
  ["cc-dd-lat","cc-dd-lon"].forEach(function (id) {
    el[id].addEventListener("blur", commitDD);
    el[id].addEventListener("keydown", function (e) { if (e.key === "Enter") { commitDD(); el[id].blur(); } });
  });

  // ---------- Eventi: DMS ----------
  function commitDMS() {
    var lat = dmsToDd(el["cc-dms-lat-d"].value, el["cc-dms-lat-m"].value, el["cc-dms-lat-s"].value, el["cc-dms-lat-h"].value === "S");
    var lon = dmsToDd(el["cc-dms-lon-d"].value, el["cc-dms-lon-m"].value, el["cc-dms-lon-s"].value, el["cc-dms-lon-h"].value === "W");
    setLatLon(lat, lon, true);
  }
  ["cc-dms-lat-d","cc-dms-lat-m","cc-dms-lat-s","cc-dms-lat-h","cc-dms-lon-d","cc-dms-lon-m","cc-dms-lon-s","cc-dms-lon-h"].forEach(function (id) {
    el[id].addEventListener("blur", commitDMS);
    el[id].addEventListener("change", commitDMS);
    el[id].addEventListener("keydown", function (e) { if (e.key === "Enter") { commitDMS(); el[id].blur(); } });
  });

  // ---------- Eventi: DMM ----------
  function commitDMM() {
    var lat = dmmToDd(el["cc-dmm-lat-d"].value, el["cc-dmm-lat-m"].value, el["cc-dmm-lat-h"].value === "S");
    var lon = dmmToDd(el["cc-dmm-lon-d"].value, el["cc-dmm-lon-m"].value, el["cc-dmm-lon-h"].value === "W");
    setLatLon(lat, lon, true);
  }
  ["cc-dmm-lat-d","cc-dmm-lat-m","cc-dmm-lat-h","cc-dmm-lon-d","cc-dmm-lon-m","cc-dmm-lon-h"].forEach(function (id) {
    el[id].addEventListener("blur", commitDMM);
    el[id].addEventListener("change", commitDMM);
    el[id].addEventListener("keydown", function (e) { if (e.key === "Enter") { commitDMM(); el[id].blur(); } });
  });

  // ---------- Eventi: UTM ----------
  function commitUTM() {
    var zoneStr = (el["cc-utm-zone"].value || "").trim().toUpperCase();
    var match = zoneStr.match(/^(\d{1,2})\s*([A-Z])$/);
    var e = parseFloat(el["cc-utm-e"].value);
    var n = parseFloat(el["cc-utm-n"].value);
    if (!match || isNaN(e) || isNaN(n)) {
      el["cc-utm-zone"].classList.toggle("cc-invalid", !match);
      el["cc-utm-e"].classList.toggle("cc-invalid", isNaN(e));
      el["cc-utm-n"].classList.toggle("cc-invalid", isNaN(n));
      return;
    }
    var zone = parseInt(match[1], 10);
    var letter = match[2];
    var ll = utmToLL(zone, letter, e, n);
    setLatLon(ll.lat, ll.lon, true);
  }
  ["cc-utm-zone","cc-utm-e","cc-utm-n"].forEach(function (id) {
    el[id].addEventListener("blur", commitUTM);
    el[id].addEventListener("keydown", function (e) { if (e.key === "Enter") { commitUTM(); el[id].blur(); } });
  });

  // ---------- Mappa: clic e trascinamento marker ----------
  map.on("click", function (e) { setLatLon(e.latlng.lat, e.latlng.lng, false); });
  marker.on("dragend", function () { var p = marker.getLatLng(); setLatLon(p.lat, p.lng, false); });

  // ---------- Copia ----------
  document.querySelectorAll("[data-copy]").forEach(function (btn) {
    btn.addEventListener("click", function () {
      var text = document.getElementById(btn.getAttribute("data-copy")).textContent;
      if (navigator.clipboard) {
        navigator.clipboard.writeText(text).then(function () { toast("Copiato"); });
      } else {
        var ta = document.createElement("textarea");
        ta.value = text; document.body.appendChild(ta); ta.select();
        document.execCommand("copy"); document.body.removeChild(ta);
        toast("Copiato");
      }
    });
  });

  // ---------- Geolocalizzazione ----------
  el["cc-geoloc-btn"].addEventListener("click", function () {
    if (!navigator.geolocation) { toast("Geolocalizzazione non disponibile"); return; }
    el["cc-geoloc-btn"].textContent = "Localizzazione…";
    navigator.geolocation.getCurrentPosition(function (pos) {
      setLatLon(pos.coords.latitude, pos.coords.longitude, true);
      el["cc-geoloc-btn"].textContent = "📍 La mia posizione";
    }, function () {
      toast("Posizione non disponibile");
      el["cc-geoloc-btn"].textContent = "📍 La mia posizione";
    });
  });

  // ---------- Ricerca indirizzo (Nominatim / OpenStreetMap) ----------
  function doSearch() {
    var q = el["cc-search"].value.trim();
    if (!q) return;
    el["cc-search-btn"].textContent = "…";
    fetch("https://nominatim.openstreetmap.org/search?format=json&limit=1&q=" + encodeURIComponent(q))
      .then(function (r) { return r.json(); })
      .then(function (results) {
        el["cc-search-btn"].textContent = "Cerca";
        if (results && results.length) {
          setLatLon(parseFloat(results[0].lat), parseFloat(results[0].lon), true);
        } else {
          toast("Nessun risultato");
        }
      })
      .catch(function () {
        el["cc-search-btn"].textContent = "Cerca";
        toast("Ricerca non riuscita");
      });
  }
  el["cc-search-btn"].addEventListener("click", doSearch);
  el["cc-search"].addEventListener("keydown", function (e) { if (e.key === "Enter") doSearch(); });

  // ---------- Avvio ----------
  render();
})();
