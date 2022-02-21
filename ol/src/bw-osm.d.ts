interface BwOsmPlugin {
  settings: Settings;
  data: MapInstance[];
  lang: Record<string, string>;
}
declare const bwOsmPlugin: BwOsmPlugin;

declare const ajaxurl: string;
