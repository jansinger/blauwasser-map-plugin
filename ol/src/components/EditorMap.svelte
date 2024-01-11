<script lang="ts">
  import { baseMap } from "$lib/baseMap";
  import { lang, settings } from "$lib/settings";
  import { fromLonLat, toLonLat } from "ol/proj";
  import { onDestroy, onMount } from "svelte";
  import type { Map } from "ol";
  import { singleFeature } from "$lib/layers/singlefeature";
  import { defaultMarkerStyle } from "$lib/styles/defaultMarkerStyle";
  import { clusterSource, geojson } from "$lib/layers/geojson";
  import { addLoadingOverlay } from "$lib/addLoadingOverlay";
  import { FitLayerControl } from "$lib/control/FitLayerControl";
  import { fly } from "svelte/transition";

  export let center: number[];
  export let zoom = settings.zoom;
  export let taxonomies = "";
  export let categories = [];
  export let tags = [];

  let map: Map;
  let apiSrc: URL;
  let lon = "-";
  let lat = "-";
  let mapElement: HTMLElement;
  let loadingOverlay: HTMLElement;
  let layer: ReturnType<typeof geojson>;
  let warning = "";

  export const updateSize = () => map.updateSize();

  onMount(async () => {
    loadingOverlay = addLoadingOverlay(mapElement);
    const projectedCenter = fromLonLat(center ?? settings.center);
    map = await baseMap(mapElement, projectedCenter, zoom);
    const style = defaultMarkerStyle(settings.assetsUrl);
    const api = new URL(settings.restApi);
    api.searchParams.set("taxonomies", taxonomies);
    api.searchParams.set("categories", categories.join(","));
    api.searchParams.set("tags", tags.join(","));
    apiSrc = api;
    layer = geojson(apiSrc.href, style);
    map.addLayer(layer);
    map.addControl(new FitLayerControl({ layer }));
    map.getView().on("change:resolution", () => {
      zoom = map.getView().getZoom();
    });
    map.getView().on("change:center", () => {
      center = toLonLat(map.getView().getCenter());
    });
  });

  onDestroy(() => {
    map.dispose();
  });

  // Update zoom on input change
  $: zoom && map && map.getView().setZoom(zoom);

  // Update center on input change
  $: {
    if (center && map) {
      map.getView().setCenter(fromLonLat(center));
      [lon, lat] = center.map((n) => Number(n).toFixed(4));
    }
  }

  // Update geojson source
  $: {
    if (apiSrc && layer) {
      apiSrc.searchParams.set("taxonomies", taxonomies);
      apiSrc.searchParams.set("categories", categories.join(","));
      apiSrc.searchParams.set("tags", tags.join(","));
      const src = clusterSource(apiSrc.href);
      src.getSource().on("featuresloadend", () => {
        loadingOverlay.classList.add("hidden");
        warning = "";
      });
      src.getSource().on("featuresloaderror", (e) => {
        loadingOverlay.classList.add("hidden");
        warning = "Keine Beiträge mit diesem Filter gefunden!";
      });
      loadingOverlay.classList.remove("hidden");
      layer.setSource(src);
    }
  }
</script>

<div class:open={warning} class="notice notice-error inline" transition:fly>
  {warning}
</div>

<p>
  {lang.center}: &phi; {lat}&deg;&nbsp;&lambda;{lon}&deg;, {lang.zoom}: {Number(
    zoom
  ).toFixed(2)}
</p>
<div class="bw-osm-map" bind:this={mapElement} />

<style lang="scss">
  .bw-osm-map {
    width: 100%;
    height: 450px;
    margin: 10px auto;
    :global(.fit-layer) {
      top: 65px;
      left: 0.5em;
    }

    :global(.ol-touch .fit-layer) {
      top: 80px;
    }
  }
</style>
