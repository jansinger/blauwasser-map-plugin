<script lang="ts" context="module">
  import { settings } from "$lib/settings";

  const categoriesUrl = `${settings.restBase}wp/v2/categories?_fields=name`;
  const tagsUrl = `${settings.restBase}wp/v2/tags?_fields=name`;
</script>

<script lang="ts">
  import EditorMap from "./EditorMap.svelte";
  import ShortcodeMulti from "./ShortcodeMulti.svelte";
  import Tags from "./Tags.svelte";

  export let center = settings.center;
  export let zoom = settings.zoom;

  let categories = [];
  let tags = [];

  const handleCategories = (event: CustomEvent) => {
    categories = event.detail.tags;
  };
  const handleTags = (event: CustomEvent) => {
    tags = event.detail.tags;
  };

  const categoriesAutocomplete = async (text: string) => {
    if (text.length < 3) {
      return [];
    }
    const list = await fetch(`${categoriesUrl}&search=${text}`);
    const res = await list.json();
    return res.map((val) => val.name);
  };
  const tagsAutocomplete = async (text: string) => {
    if (text.length < 3) {
      return [];
    }
    const list = await fetch(`${tagsUrl}&search=${text}`);
    const res = await list.json();
    return res.map((val) => val.name);
  };
</script>

<div class="tag-cloud">
  <Tags
    on:tags={handleCategories}
    addKeys={[9, 13]}
    allowPaste={true}
    allowDrop={true}
    splitWith={"/"}
    onlyUnique={true}
    placeholder={"Kategorien"}
    autoComplete={categoriesAutocomplete}
    name={"categories"}
    allowBlur={true}
    disable={false}
    minChars={3}
  />
  <span class="td-page-o-info">Filter für Beiträge nach Kategorien.</span>
</div>
<div class="tag-cloud">
  <Tags
    on:tags={handleTags}
    addKeys={[9, 13]}
    allowPaste={true}
    allowDrop={true}
    splitWith={"/"}
    onlyUnique={true}
    placeholder={"Schlagworte"}
    autoComplete={tagsAutocomplete}
    name={"tags"}
    allowBlur={true}
    disable={false}
    minChars={3}
  />
  <span class="td-page-o-info">Filter für Beiträge nach Schlagworten.</span>
</div>

<EditorMap bind:center bind:zoom {categories} {tags} />
<ShortcodeMulti {center} {zoom} {categories} {tags} />

<style lang="scss">
  .tag-cloud {
    margin-top: 10px;
  }
</style>
