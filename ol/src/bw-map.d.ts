interface BwMapPlugin {
  settings: Settings;
  data: MapInstance[];
  lang: Record<string, string>;
}
declare const bwMapPlugin: BwMapPlugin;

declare const ajaxurl: string;
