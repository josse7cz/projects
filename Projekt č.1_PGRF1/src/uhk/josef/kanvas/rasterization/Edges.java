package uhk.josef.kanvas.rasterization;

import java.io.Serializable;
import java.util.ArrayList;

/**
 * Trida pro seznam usecek
 * 
 * @author Josef Janda
 * 
 */
public class Edges implements Serializable {
	private static final long serialVersionUID = 1L;//
	private static ArrayList<Edge> edges = new ArrayList<>();//

	public void add(Edge p) {
		edges.add(p);
	}

	public void smazatVse() {
		edges.clear();
	}

	public int pocetUsecek() {
		return edges.size();
	}

	public void add(int x1, int x2, int y1, int y2, int color) {

	}

	public void draw(LineRasterizer liner) {
		for (int i = 0; i < pocetUsecek(); i++) {
			edges.get(i).draw(liner);
		}

	}

	@SuppressWarnings("static-access")
	public Edge get(int i) {
		return this.edges.get(i);

	}

}
