<script type="ts">
  import { baseMap } from "$lib/baseMap";
  import { settings } from "$lib/settings";
  import { fromLonLat, toLonLat } from "ol/proj";
  import { onMount } from "svelte";
  import type { Map } from "ol";
  import { singleFeature } from "$lib/layers/singlefeature";
  import Point from "ol/geom/Point";
  import { movableMarker } from "$lib/layers/movableMarker";
  import { modifyInteraction } from "$lib/admin/modifyInteraction";
  import { ResetCenterControl } from "$lib/control/ResetCenterControl";

  export let map: Map = null;
  export let center: number[];
  export let zoom: number;

  let mapElement: HTMLElement;
  let layer: ReturnType<typeof singleFeature>;
  let point: Point;

  onMount(async () => {
    const projectedCenter = fromLonLat(center ?? settings.center);
    map = baseMap(mapElement, projectedCenter, zoom);
    ({ layer, point } = movableMarker(projectedCenter));
    layer.setVisible(!!center);
    map.addLayer(layer);
    map.addControl(new ResetCenterControl({ geometry: point }));
    const modify = modifyInteraction(layer.getSource());
    map.addInteraction(modify);
    map
      .getView()
      .on("change:resolution", () => (zoom = map.getView().getZoom()));
    map.on("singleclick", (e) => (center = toLonLat(e.coordinate)));
    modify.on(
      "modifyend",
      (e) => (center = toLonLat(e.mapBrowserEvent.coordinate))
    );
  });

  $: {
    if (layer) {
      layer.setVisible(!!center);
      if (center) {
        const projectedCenter = fromLonLat(center ?? settings.center);
        point.setCoordinates(projectedCenter);
      }
    }
  }
</script>

<div class="bw-osm-map" bind:this={mapElement} />

<style lang="scss">
  .bw-osm-map {
    width: 100%;
    height: 450px;
    margin: 10px auto;

    :global(.reset-center) {
      top: 65px;
      left: 0.5em;
    }

    :global(.ol-touch .reset-center) {
      top: 80px;
    }
  }
</style>
