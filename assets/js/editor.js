(function () {
  const parent = document.getElementById("vc_ui-panel-edit-element");
  if (parent) {
    const mapElement = parent.querySelector(".bw-map-editor");
    bwOsm.showEditorMap(parent, mapElement);
  }
})();
