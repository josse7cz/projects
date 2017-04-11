package uhk.josef.kanvas.rasterization;

/**
 * Tøída pro bod
 * @author Josef Janda 2016
 *
 */
public class Point {
	private final int p1, p2;

	public Point(final int p1, final int p2) {
		this.p1 = p1;
		this.p2 = p2;
	}

	public int getX() {
		return p1;
	}

	public int getY() {
		return p2;
	}
}
