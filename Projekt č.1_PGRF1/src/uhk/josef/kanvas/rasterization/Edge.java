package uhk.josef.kanvas.rasterization;

public class Edge {
	private int x1, x2, y1, y2, color;

	/**
	 * Tøída pro úseèku a Konstruktor metoda pro kresbu úseèky
	 * 
	 * @param pocX
	 * @param pocY
	 * @param konX
	 * @param konY
	 * @param color
	 */

	public Edge(int x1, int y1, int x2, int y2, int color) {
		this.x1 = x1;
		this.x2 = x2;
		this.y1 = y1;
		this.y2 = y2;
		this.color = color;
	}

	public void draw(LineRasterizer liner) {
		liner.drawLine(x1, y1, x2, y2, color);

	}

	public int getX1() {
		return x1;
	}

	public int getY1() {
		return y1;
	}

	public int getX2() {
		return x2;
	}

	public int getY2() {
		return y2;
	}

	public int getSize() {

		return (int) Math.sqrt(Math.pow(x2 - x1, 2) + Math.pow(y2 - y1, 2));
	}

	public void setSize(int size) {

	}

}
