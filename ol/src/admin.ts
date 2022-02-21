import "./style.admin.scss";
import Metabox from "./components/Metabox.svelte";
import WpEditorMap from "./components/WpEditorMap.svelte";

export const showAdminMap = (data: { center: number[]; nonce: string }) => {
  return new Metabox({
    target: document.getElementById("bw-osm-svelte"),
    props: { ...data },
  });
};

export const showEditorMap = (parent: HTMLElement, target: HTMLElement) => {
  return new WpEditorMap({
    target,
    props: { parent },
  });
};

/* @ts-ignore */
if (import.meta.env.DEV && window) {
  window["bwOsm"] = {
    showAdminMap,
    showEditorMap,
  };
}
