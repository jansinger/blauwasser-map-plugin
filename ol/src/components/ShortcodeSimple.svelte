<script lang="ts">
  import { settings } from "$lib/settings";

  export let zoom = 5;
  let copyText = "Copy";
  let code: HTMLElement;
  let zoomAttr = "";

  $: zoomAttr =
    Number(zoom) === Number(settings.zoom) ? "" : `zoom="${zoom.toFixed(2)}"`;

  const copyCode = () => {
    navigator.clipboard.writeText(code.innerText);
    copyText = "Copied";
    setTimeout(() => (copyText = "Copy"), 1000);
  };
</script>

<pre id="bw-osm-code"><code bind:this={code}>[bw-map {zoomAttr}/]</code
  >{#if navigator.clipboard}<button on:click|preventDefault={copyCode}
      >{copyText}</button
    >{/if}</pre>
