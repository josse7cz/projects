package uhk.josef.kanvas.rasterization;

import java.awt.image.BufferedImage;
import java.util.ArrayList;
import java.util.Collections;
import java.util.LinkedList;
import java.util.List;

/**
 * @author Josef Janda 2016 Tøída pro hrany a pro vyplòování scanlinem
 */
public class PolygonRasterizerScanline implements PolygonRasterizer {
	private final BufferedImage img;

	public PolygonRasterizerScanline(final BufferedImage img) {
		this.img = img;
	}

	private static class Line {
		private final Point p1, p2;
		private double k, q;

		public Line(final Point p1, final Point p2) {// zmìna orientace odshora
														// dolu
			if (p1.getY() <= p2.getY()) {
				this.p1 = p1;
				this.p2 = p2;
			} else {
				this.p1 = p2;
				this.p2 = p1;
			}
			// uvazujeme x = k * y + q
			k = (double) (p2.getX() - p1.getX()) / (p2.getY() - p1.getY());
			q = p1.getX() - k * p1.getY();

		}

		public boolean isHorizontal() {
			return (p1.getY() == p2.getY());
		}

		public boolean intersects(final int y) {
			return (p1.getY() < y && y <= p2.getY());
		}

		public double intersection(final int y) {
			float dx = p1.getX() - p2.getX();
			float dy = p1.getY() - p2.getY();
			float c = dx / dy;
			float x = c * (y - p1.getY()) + p1.getX();
			return (int) x;

		}
	}

	@Override
	public PolygonRasterizerScanline drawPolygon(final List<Point> points) {
		final List<Line> listU = new LinkedList<Line>();// list hran
		final ArrayList<Integer> listPrus = new ArrayList<Integer>();// list
																		// pruseciku
		int yMin = img.getHeight(), yMax = 0;

		for (int i = 0; i < points.size(); i++) {
			Line l = new Line(points.get(i), points.get((i + 1) % points.size())); // vcetneuzavreni

			if (!l.isHorizontal()) {
				listU.add(l);
				if (yMin > l.p1.getY()) {
					yMin = l.p1.getY();
				}
				if (yMax < l.p2.getY()) {
					yMax = l.p2.getY();
				}
			}
		}

		for (int y = yMin; y <= yMax; y++) {// projit vsechny radky
			listPrus.clear();
			for (Line line : listU) {// projit vsechny usecky
				if (line.intersects(y)) {// najit pruseciky
					listPrus.add((int) line.intersection(y));// pridani
																// pruseciku do
																// seznamu
				}

			}

			Collections.sort(listPrus);
			boolean tr = true;
			for (int t = 0; t < listPrus.size() - 1; t++) {
				if (tr) {
					for (int X = listPrus.get(t); X < listPrus.get(t + 1); X++) {
						img.setRGB(X, y, 0xff0000);

					}

				}
				tr = !tr;
			}

		}

		return this;
	}
}
