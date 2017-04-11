package uhk.josef.kanvas.rasterization;

public interface LineRasterizer {
	LineRasterizer drawLine(int x1, int y1, int x2, int y2, int color);
	LineRasterizer drawLineAA(int x1, int y1, int x2, int y2, int color);
}
