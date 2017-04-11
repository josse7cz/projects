package uhk.josef.kanvas.rasterization;

public interface CircleRasterizer {//interface pro kruh a vysec

	CircleRasterizer drawCircle(int x1, int y1, int x2, int y2, final int color);
	CircleRasterizer drawCirclePart(int x1, int y1, int x2, int y2, int x3,int y3,final int color);
	}
