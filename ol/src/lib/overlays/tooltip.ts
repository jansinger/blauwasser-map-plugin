import { Overlay } from "ol";
import type { Feature, Map, MapBrowserEvent } from "ol";
import type Layer from "ol/layer/Layer";
import type Source from "ol/source/Source";
import type BaseEvent from "ol/events/Event";
import type Geometry from "ol/geom/Geometry";
import type RenderFeature from "ol/render/Feature";
import LayerRenderer from "ol/renderer/Layer";

const getFeatureAtEventPixel = (event: MapBrowserEvent<UIEvent>, map: Map) => {
  const layerFilter = (candidate: Layer<Source, LayerRenderer<any>>) => {
    return candidate.get("type") === "FeatureLayer";
  };
  const pixel = map.getEventPixel(event.originalEvent);
  return map.forEachFeatureAtPixel(
    pixel,
    function (feature) {
      return feature;
    },
    { layerFilter }
  );
};

export const createTooltipOverlay = (
  element: HTMLElement,
  map: Map
): Overlay => {
  const overlay = new Overlay({
    element: element,
    offset: [5, 0],
    positioning: "bottom-left",
    autoPan: {
      animation: {
        duration: 250,
      },
    },
  });

  const clickHandler = () => {
    return function (evt: MapBrowserEvent<UIEvent>) {
      const f = getFeatureAtEventPixel(evt, map);
      if (f) {
        hideTooltip();
        map.dispatchEvent({
          type: "clickFeature",
          detail: f,
        } as unknown as BaseEvent);
      }
    };
  };

  const showEntryPreview = (feat: RenderFeature | Feature<Geometry>) => {
    const title = feat.get("title");
    const picture = feat.get("image");
    const content = `<div class="right glass">
		${picture}
		<div class="text-content">
			<p>${title}</p>
		</div>
		<i></i>
		</div>`;
    element.innerHTML = content;
  };

  let feature: Feature<Geometry> | RenderFeature = null;

  const hideTooltip = () => {
    feature = undefined;
    map.getTargetElement().style.cursor = "";
    overlay.setPosition(undefined);
  };

  const pointermoveHandler = () => {
    return (evt: MapBrowserEvent<UIEvent>) => {
      const newfeature = getFeatureAtEventPixel(evt, map);
      if (feature === newfeature) {
        // only set new position if feature has not changed
        feature && overlay.setPosition(evt.coordinate);
        return;
      }
      if (newfeature) {
        const features = newfeature.get("features");
        if (!features || features?.length === 1) {
          feature = newfeature;
          const entryFeature = features ? features[0] : feature;
          map.getTargetElement().style.cursor = "pointer";
          showEntryPreview(entryFeature);
          overlay.setPosition(evt.coordinate);
        }
      } else {
        hideTooltip();
      }
    };
  };

  map.addOverlay(overlay);

  // display popup on click
  map.on("click", clickHandler());

  // change mouse cursor when over marker
  map.on("pointermove", pointermoveHandler());

  return overlay;
};
