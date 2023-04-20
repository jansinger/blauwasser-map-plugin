<script lang="ts">
  import GeotagEditor from "./GeotagEditor.svelte";
  import { fade } from "svelte/transition";
  import MultimarkerEditor from "./MultimarkerEditor.svelte";
  import { settings } from "$lib/settings";

  export let center: number[];
  export let zoom: number = settings.zoom;
  export let nonce: string;

  const geo = Symbol("geo");
  const multi = Symbol("multi");

  type Tab = typeof geo | typeof multi;

  let selected = geo;

  const selectTab = (tab: Tab) => {
    selected = tab;
  };
</script>

<div class="td-page-options-tab-wrap">
  <div
    class="td-page-options-tab"
    class:td-page-options-tab-active={selected === geo}
  >
    <button
      disabled={selected === geo}
      on:click|preventDefault={() => selectTab(geo)}>Geotag</button
    >
  </div>
  <div
    class="td-page-options-tab"
    class:td-page-options-tab-active={selected === multi}
  >
    <button
      disabled={selected === multi}
      on:click|preventDefault={() => selectTab(multi)}>Multimarker</button
    >
  </div>
</div>
<div class="td-meta-box-inside">
  <div
    class="td-page-option-panel td-post-option-ship td-page-option-panel-active"
  >
    {#if selected === geo}
      <div transition:fade>
        <GeotagEditor {center} {zoom} {nonce} />
      </div>
    {/if}
    {#if selected === multi}
      <div transition:fade>
        <MultimarkerEditor {center} {zoom} />
      </div>
    {/if}
  </div>
</div>

<style lang="scss">
  .td-page-options-tab button {
    display: block;
    padding: 10px 14px;
    text-decoration: none;
    color: #000;
    font-weight: 500;
    font-size: 14px;
    border: none;
    background-color: transparent;
    cursor: pointer;
  }
</style>
