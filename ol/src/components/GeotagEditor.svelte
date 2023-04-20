<script lang="ts">
  import { fly } from "svelte/transition";
  import MapComponent from "./GeotagMap.svelte";
  import ShortCodeSimple from "./ShortcodeSimple.svelte";
  import type { Map } from "ol";
  import { lang } from "$lib/settings";
  import { doAjax } from "$lib/admin/doAjax";

  export let center: number[];
  export let zoom = 5;
  export let nonce = "";
  let originalCenter: number[];
  let warningType = "warning";
  let warning: string;

  let map: Map;
  let lon = "-";
  let lat = "-";

  originalCenter = center;
  $: {
    if (center) {
      [lon, lat] = center.map((n) => Number(n).toFixed(4));
      if (center !== originalCenter) {
        warning = lang.changePosition;
      }
    } else {
      lon = lat = "-";
    }
  }

  const resetCenter = () => (center = originalCenter);

  const save = async () => {
    if (center === originalCenter) {
      warning = lang.noNewPosition;
      return;
    }
    const data = {
      action: "bw_osm_save_geotag",
      geotag: `${lat},${lon}`,
    };
    const { response, result } = await doAjax(nonce, data);
    if (response.status === 200) {
      warningType = "success";
      warning = lang.positionSaved;
      originalCenter = center;
    } else {
      warningType = "error";
      warning = `${lang.saveError} ${result}`;
    }
  };

  const deleteGeotag = async () => {
    const data = {
      action: "bw_osm_delete_geotag",
    };
    const { response, result } = await doAjax(nonce, data);
    if (response.status === 200) {
      warningType = "success";
      warning = lang.positionDeleted;
      center = originalCenter = undefined;
    } else {
      warningType = "error";
      warning = `${lang.saveError} ${result}`;
    }
  };
</script>

<div
  class:open={warning}
  class="notice notice-{warningType} inline"
  transition:fly
>
  {warning}
</div>
<p>
  {lang.geotag}: &phi; {lat}&deg;&nbsp;&lambda;{lon}&deg;, {lang.zoom}: {Number(
    zoom
  ).toFixed(2)}
</p>
<MapComponent bind:center bind:zoom bind:map />
<ShortCodeSimple {zoom} />
<span class="td-page-o-info">{lang.shortcodeHint}</span>
<div class="td-meta-box-row">
  <button
    id="bw-osm-map-admin-save"
    disabled={center === originalCenter}
    on:click={save}
    class="button button-primary"
    type="button">{lang.save}</button
  >
  <button
    id="bw-osm-map-admin-reset"
    class="button button-secondary"
    type="button"
    on:click={resetCenter}>{lang.resetGeotag}</button
  >
  <button
    id="bw-osm-map-admin-delete"
    class="button button-secondary"
    on:click={deleteGeotag}
    type="button">{lang.deleteGeotag}</button
  >
</div>
