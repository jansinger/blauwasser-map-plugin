<script lang="ts">
  import { settings } from "$lib/settings";

  export let zoom = 5;
  export let center = [0, 0];
  export let categories = [];
  export let tags = [];

  let copyText = "Copy";
  let code: HTMLElement;
  let zoomAttr = "";
  let categoriesAttr = "";
  let tagsAttr = "";
  let lon = "0";
  let lat = "0";

  $: center && ([lon, lat] = center.map((n) => Number(n).toFixed(4)));

  $: zoomAttr =
    Number(zoom) === Number(settings.zoom) ? "" : `zoom="${zoom.toFixed(2)}" `;

  $: categoriesAttr =
    categories?.length > 0 ? `categories="${categories}" ` : "";
  $: tagsAttr = tags?.length > 0 ? `tags="${tags}" ` : "";

  const copyCode = () => {
    navigator.clipboard.writeText(code.innerText);
    copyText = "Copied";
    setTimeout(() => (copyText = "Copy"), 1000);
  };
</script>

<pre id="bw-osm-code"><code bind:this={code}
    >[bw-map center="{lat}, {lon}" {categoriesAttr}{tagsAttr}{zoomAttr}/]</code
  >{#if navigator.clipboard}<button on:click|preventDefault={copyCode}
      >{copyText}</button
    >{/if}</pre>
