<script type="ts">
  import { settings } from "$lib/settings";
  import { onMount } from "svelte";
  import EditorMap from "./EditorMap.svelte";

  export let parent: HTMLElement;

  let editorMap: EditorMap;

  let center: number[] = settings.center;
  let zoom = settings.zoom;
  let taxonomies: string;

  // Input elements
  let zoomElement: HTMLInputElement;
  let centerElement: HTMLInputElement;
  let taxonomiesElement: HTMLInputElement;
  let resizeObserver: ResizeObserver;

  const setCenterFromInput = () => {
    center = String(centerElement.value)
      .split(",")
      .map((s) => Number(s))
      .reverse();
  };

  const setZoomFromInput = () => {
    zoom = Number(zoomElement.value);
  };

  const setTaxonomiesFromInput = () => {
    taxonomies = String(taxonomiesElement.value);
  };

  const observer = new MutationObserver(function () {
    setTaxonomiesFromInput();
  });

  const onunmount = () => {
    observer.disconnect();
    resizeObserver.disconnect();
  };

  onMount(() => {
    zoomElement = parent.querySelector("input[name=zoom].wpb_vc_param_value");
    centerElement = parent.querySelector(
      "input[name=center].wpb_vc_param_value"
    );
    taxonomiesElement = parent.querySelector(
      ".bw-map-taxonomies input[name=taxonomies].wpb_vc_param_value"
    );
    if (!taxonomiesElement) {
      return;
    }
    if (window.innerWidth > 1024) {
      parent.style.left = window.innerWidth - 800 + "px";
      parent.style.width = "800px";
    }
    parent.style["max-height"] = "none";
    observer.observe(taxonomiesElement, {
      attributes: true,
      attributeFilter: ["value"],
    });
    setCenterFromInput();
    setZoomFromInput();
    setTaxonomiesFromInput();
    resizeObserver = new ResizeObserver(() => editorMap.updateSize());
    resizeObserver.observe(parent);
    return onunmount;
  });

  // Updates from the map
  $: zoom && zoomElement && (zoomElement.value = Number(zoom).toFixed(2));

  $: {
    if (center && centerElement) {
      centerElement.value = center
        .map((v) => Number(v).toFixed(4))
        .reverse()
        .join(", ");
    }
  }
</script>

<EditorMap bind:zoom bind:center bind:taxonomies bind:this={editorMap} />
