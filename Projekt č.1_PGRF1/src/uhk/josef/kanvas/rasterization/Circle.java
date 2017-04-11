package uhk.josef.kanvas.rasterization;

/**Tøída pro kruh
 * @author Josef Janda
 *
 */
public class Circle {

	private int x1, y1,x2, y2, color;

	/**
	 * Konstruktor
	 * 
	 * @param pocX
	 * @param pocY
	 * @param konX
	 * @param konY
	 * @param color
	 */

	public Circle(int x1, int y1,int x2,int y2, final int color) {
		this.x1 = x1;
		this.y1 = y1;
		this.x2 = x2;
		this.y2 = y2;
		this.color = color;
	}
	public void drawCircle(CircleRasterizationTrivial circler) {
		circler.drawCircle(x1, y1, x2, y2, color);
		
	}

	public int getX1() {
		return x1;
	}

	public int getY1() {
		return y1;
	}

}