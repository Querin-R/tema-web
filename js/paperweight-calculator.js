(function() {
  var widgets = document.querySelectorAll('.qdpw-widget');
  var root = widgets[widgets.length - 1];

  var system = 'metric';
  var LB_TO_GSM_CONST = 1406.3;
  var MM_PER_IN = 25.4;
  var G_PER_OZ = 28.3495;
  var G_PER_LB = 453.592;

  var state = { length: 420, width: 297, gsm: 80, sheets: 1, extra: 0, copies: 1 };
  var extraOn = false, runOn = false;

  function fmt(n, d) { return n.toLocaleString('it-IT', { minimumFractionDigits: d, maximumFractionDigits: d }); }

  function mmToDisplay(mm) { return system === 'imperial' ? mm / MM_PER_IN : mm; }
  function displayToMm(v) { return system === 'imperial' ? v * MM_PER_IN : v; }

  var sliderDefs = [
    { key: 'length', label: 'Lunghezza', unitKey: 'dim', min: function(){return system==='imperial'?0.5:10;}, max: function(){return system==='imperial'?48:1200;}, step: function(){return system==='imperial'?0.1:1;}, decimals: function(){return system==='imperial'?2:0;} },
    { key: 'width', label: 'Larghezza', unitKey: 'dim', min: function(){return system==='imperial'?0.5:10;}, max: function(){return system==='imperial'?48:1200;}, step: function(){return system==='imperial'?0.1:1;}, decimals: function(){return system==='imperial'?2:0;} },
    { key: 'gsm', label: 'Grammatura', unitKey: 'gsm', min: function(){return 10;}, max: function(){return 500;}, step: function(){return 1;}, decimals: function(){return 0;} },
    { key: 'sheets', label: 'Fogli/copia', unitKey: 'sheets', min: function(){return 1;}, max: function(){return 500;}, step: function(){return 1;}, decimals: function(){return 0;} }
  ];
  var extraDefs = [
    { key: 'extra', label: 'Peso agg.', unitKey: 'g', min: function(){return 0;}, max: function(){return 1000;}, step: function(){return 1;}, decimals: function(){return 0;} }
  ];
  var runDefs = [
    { key: 'copies', label: 'Copie', unitKey: 'copie', min: function(){return 1;}, max: function(){return 10000;}, step: function(){return 1;}, decimals: function(){return 0;} }
  ];

  function unitLabel(unitKey) {
    if (unitKey === 'dim') return system === 'imperial' ? 'in' : 'mm';
    if (unitKey === 'gsm') return 'g/m²';
    if (unitKey === 'g') return 'g';
    if (unitKey === 'sheets') return '';
    if (unitKey === 'copie') return '';
    return '';
  }

  function getDisplayValue(def) {
    var v = state[def.key];
    if (def.unitKey === 'dim') v = mmToDisplay(v);
    return v;
  }
  function setFromDisplayValue(def, displayVal) {
    var v = displayVal;
    if (def.unitKey === 'dim') v = displayToMm(v);
    state[def.key] = v;
  }

  function buildSliderRow(def) {
    var row = document.createElement('div');
    row.className = 'qdpw-slider-row';
    row.dataset.key = def.key;

    var label = document.createElement('label');
    label.textContent = def.label;
    row.appendChild(label);

    var range = document.createElement('input');
    range.type = 'range';
    row.appendChild(range);

    var valCell = document.createElement('div');
    valCell.className = 'qdpw-value-cell';
    var span = document.createElement('span');
    span.className = 'qdpw-value-span';
    var editBtn = document.createElement('button');
    editBtn.type = 'button';
    editBtn.className = 'qdpw-edit-btn';
    editBtn.innerHTML = '&#9998;';
    valCell.appendChild(span);
    valCell.appendChild(editBtn);
    row.appendChild(valCell);

    function refreshRange() {
      range.min = def.min();
      range.max = def.max();
      range.step = def.step();
      range.value = getDisplayValue(def);
    }
    function refreshLabel() {
      var d = def.decimals();
      var u = unitLabel(def.unitKey);
      span.textContent = fmt(getDisplayValue(def), d) + (u ? u : '');
    }
    refreshRange();
    refreshLabel();

    range.addEventListener('input', function() {
      setFromDisplayValue(def, parseFloat(range.value));
      refreshLabel();
      if (def.key === 'length' || def.key === 'width') { markCustomFormat(); }
      recalc();
    });

    editBtn.addEventListener('click', function() {
      var input = document.createElement('input');
      input.type = 'number';
      input.step = 'any';
      input.value = getDisplayValue(def);
      valCell.replaceChild(input, span);
      valCell.removeChild(editBtn);
      input.focus();
      function commit() {
        var v = parseFloat(input.value);
        if (!isNaN(v)) {
          setFromDisplayValue(def, v);
          if (v > parseFloat(range.max)) range.max = v;
          if (v < parseFloat(range.min)) range.min = v;
          range.value = v;
          refreshLabel();
          if (def.key === 'length' || def.key === 'width') { markCustomFormat(); }
          recalc();
        }
        valCell.replaceChild(span, input);
        valCell.appendChild(editBtn);
      }
      input.addEventListener('blur', commit);
      input.addEventListener('keydown', function(e) { if (e.key === 'Enter') { input.blur(); } });
    });

    row._refresh = function() { refreshRange(); refreshLabel(); };
    return row;
  }

  function buildSliderGroup(container, defs) {
    container.innerHTML = '';
    defs.forEach(function(def) { container.appendChild(buildSliderRow(def)); });
  }

  var mainContainer = root.querySelector('#qdpw-main-sliders');
  var extraContainer = root.querySelector('#qdpw-extra-sliders');
  var runContainer = root.querySelector('#qdpw-run-sliders');
  buildSliderGroup(mainContainer, sliderDefs);
  buildSliderGroup(extraContainer, extraDefs);
  buildSliderGroup(runContainer, runDefs);

  function refreshAllSliders() {
    [mainContainer, extraContainer, runContainer].forEach(function(c) {
      Array.prototype.forEach.call(c.children, function(row) { if (row._refresh) row._refresh(); });
    });
  }

  function markCustomFormat() {
    root.querySelectorAll('.qdpw-pill').forEach(function(p) { p.classList.remove('qdpw-pill-active'); });
    root.querySelector('#qdpw-more-formats').value = '';
  }

  root.querySelectorAll('.qdpw-toggle-btn').forEach(function(btn) {
    btn.addEventListener('click', function() {
      system = btn.dataset.system;
      root.querySelectorAll('.qdpw-toggle-btn').forEach(function(b) { b.classList.toggle('qdpw-active', b === btn); });
      root.querySelectorAll('.qdpw-imperial-only').forEach(function(el) { el.style.display = system === 'imperial' ? 'block' : 'none'; });
      refreshAllSliders();
      recalc();
    });
  });

  root.querySelectorAll('.qdpw-pill').forEach(function(pill) {
    pill.addEventListener('click', function() {
      root.querySelectorAll('.qdpw-pill').forEach(function(p) { p.classList.remove('qdpw-pill-active'); });
      pill.classList.add('qdpw-pill-active');
      var select = root.querySelector('#qdpw-more-formats');
      var match = select.value = pill.dataset.w + ',' + pill.dataset.h;
      if (select.value !== match) { select.value = ''; }
      state.width = parseFloat(pill.dataset.w);
      state.length = parseFloat(pill.dataset.h);
      refreshAllSliders();
      recalc();
    });
  });

  root.querySelector('#qdpw-more-formats').addEventListener('change', function(e) {
    if (!e.target.value) return;
    var parts = e.target.value.split(',');
    state.width = parseFloat(parts[0]);
    state.length = parseFloat(parts[1]);
    root.querySelectorAll('.qdpw-pill').forEach(function(p) {
      p.classList.toggle('qdpw-pill-active', p.dataset.w === parts[0] && p.dataset.h === parts[1]);
    });
    refreshAllSliders();
    recalc();
  });

  var lbToggle = root.querySelector('#qdpw-lb-toggle');
  var lbHelper = root.querySelector('#qdpw-lb-helper');
  lbToggle.addEventListener('click', function() {
    lbHelper.style.display = lbHelper.style.display === 'none' ? 'flex' : 'none';
  });
  root.querySelector('#qdpw-fromlb').addEventListener('click', function() {
    var lb = parseFloat(root.querySelector('#qdpw-basisweight').value);
    if (!lb) return;
    var cat = root.querySelector('#qdpw-category').value.split(',');
    var basisW = parseFloat(cat[0]), basisH = parseFloat(cat[1]);
    state.gsm = (lb * LB_TO_GSM_CONST) / (basisW * basisH);
    refreshAllSliders();
    recalc();
  });

  var toggleExtraBtn = root.querySelector('#qdpw-toggle-extra');
  var toggleRunBtn = root.querySelector('#qdpw-toggle-run');
  toggleExtraBtn.addEventListener('click', function() {
    extraOn = !extraOn;
    toggleExtraBtn.classList.toggle('qdpw-active', extraOn);
    extraContainer.style.display = extraOn ? 'flex' : 'none';
    if (!extraOn) { state.extra = 0; refreshAllSliders(); }
    recalc();
  });
  toggleRunBtn.addEventListener('click', function() {
    runOn = !runOn;
    toggleRunBtn.classList.toggle('qdpw-active', runOn);
    runContainer.style.display = runOn ? 'flex' : 'none';
    root.querySelector('#qdpw-readout-run-box').style.display = runOn ? 'flex' : 'none';
    if (!runOn) { state.copies = 1; refreshAllSliders(); }
    recalc();
  });

  function dualWeight(grams) {
    if (Math.abs(grams) < 1000) {
      var oz = grams / G_PER_OZ;
      return system === 'imperial' ? fmt(oz, 2) + ' oz (' + fmt(grams, 2) + ' g)' : fmt(grams, 2) + ' g (' + fmt(oz, 2) + ' oz)';
    } else {
      var kg = grams / 1000, lb = grams / G_PER_LB;
      return system === 'imperial' ? fmt(lb, 3) + ' lb (' + fmt(kg, 3) + ' kg)' : fmt(kg, 3) + ' kg (' + fmt(lb, 3) + ' lb)';
    }
  }

  function recalc() {
    var areaM2 = (state.length * state.width) / 1000000;
    var weightPerSheetG = areaM2 * state.gsm;
    var weightPerCopyG = weightPerSheetG * state.sheets + (extraOn ? state.extra : 0);
    root.querySelector('#qdpw-readout-copy').textContent = dualWeight(weightPerCopyG);
    if (runOn) {
      var totalG = weightPerCopyG * state.copies;
      root.querySelector('#qdpw-readout-run').textContent = dualWeight(totalG);
    }
  }

  recalc();
})();
