import { defineConfig } from "vite";
import path from "path";

export default defineConfig({
  resolve: {
    alias: {
      $lib: path.resolve(__dirname, "./ol/src/lib"),
    },
  },
  build: {
    sourcemap: false,
    // generate manifest.json in outDir
    manifest: false,
    rollupOptions: {
      // overwrite default .html entry
      input: ["./ol/src/main.ts"],
      output: {
        assetFileNames: "[name].[ext]",
      },
    },
    outDir: "./assets/ol",
    emptyOutDir: false,
    lib: {
      entry: path.resolve(__dirname, "ol/src/main.ts"),
      name: "bwMap",
      formats: ["umd"],
      fileName: (format) => `bw-map.${format}.js`,
    },
  },
});
