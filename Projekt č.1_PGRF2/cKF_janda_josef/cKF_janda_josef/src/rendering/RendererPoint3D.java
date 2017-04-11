package rendering;

import java.awt.Color;
import java.util.List;
import java.util.Optional;
import rasterization.LineRasterizer;
import rasterization.TriangleRasterizer;
import transforms.Mat4;
import transforms.Point3D;
import transforms.Vec3D;
/**
 * prubezna uloha cislo 1
 * 
 * @author Josef Janda
 * @version 9.4.2017
 */
public class RendererPoint3D implements Renderer<Point3D> {

	LineRasterizer liner;
	TriangleRasterizer triangler;
	private final int width, height;
	public Integer triangleColor;
	public Integer lineColor;

	public RendererPoint3D(final LineRasterizer liner, final TriangleRasterizer triangler, final int width,
			final int height) {
		this.liner = liner;
		this.triangler = triangler;
		this.width = width;
		this.height = height;
	}

	@Override
	public void render(final List<Point3D> vertices, final List<Integer> indices, final int startIndex,
			final int indexCount, final rendering.Renderer.PrimitiveType type, final Mat4 mat, List<Integer> colors) {

		if (colors.isEmpty()) {
			for (int i = 0; i < 50; i++) {
				int r = (int) (Math.random() * 255);
				int g = (int) (Math.random() * 255);
				int b = (int) (Math.random() * 255);
				colors.add(new Color(r, g, b).getRGB());
				colors.add(new Color(r, g, b).getRGB());

			}
		}

		class Impl {
			void renderLineList() {
				if (startIndex < 0 || startIndex + indexCount > indices.size() || indexCount % 2 != 0) {
					System.err.println("Illegal argument");
					return;
				}

				for (int i = startIndex; i < startIndex + indexCount; i += 2) {
					final Point3D p1 = vertices.get(indices.get(i));
					final Point3D p2 = vertices.get(indices.get(i + 1));
					lineColor = colors.get(i % colors.size());
					transformLine(p1, p2);
				}
			}

			void renderTriangleList() {
				if (startIndex < 0 || startIndex + indexCount > indices.size() || indexCount % 3 != 0) {
					System.err.println("Illegal argument");
					return;
				}
				for (int i = startIndex; i < startIndex + indexCount; i += 3) {
					final Point3D p1 = vertices.get(indices.get(i));
					final Point3D p2 = vertices.get(indices.get(i + 1));
					final Point3D p3 = vertices.get(indices.get(i + 2));
					// triangleColor = colors.get(i/3);
					triangleColor = colors.get((i % colors.size()) / 3);
					transformTriangle(p1, p2, p3);

				}
			}

			void transformTriangle(final Point3D p1, final Point3D p2, final Point3D p3) {
				final double wMin = 1e-4; // lepsi poslat z venku(tedy vlastnost
											// rendereru)//desetitisicina metru,
											// pro souradnice v rozumnych
											// metrech
				Point3D pom;
				Point3D tp1 = p1.mul(mat);
				Point3D tp2 = p2.mul(mat);
				Point3D tp3 = p3.mul(mat);
				// TODO zaridit tp1.w >= tp2.w >= tp3.w //usporadani
				// trojuhelniku podle w

				if (tp1.getW() <= tp2.getW()) {
					pom = tp1;
					tp1 = tp2;
					tp2 = pom;
				}
				if (tp2.getW() <= tp3.getW()) {
					pom = tp2;
					tp2 = tp3;
					tp3 = pom;
				}
				if (tp1.getW() <= tp2.getW()) {
					pom = tp1;
					tp1 = tp2;
					tp2 = pom;
				}

				if (tp3.getW() >= wMin) {// pokud p3 je kladne je cely
											// trojuhelnik kladny(orezani )
					dehomogTriangle(tp1, tp2, tp3);
					return;
				}
				if (tp2.getW() >= wMin) {// pokud p3 zaporne a p2 kladne
					final double ta = (wMin - tp2.getW()) / (tp3.getW() - tp2.getW());
					final Point3D pa = tp2.mul(1 - ta).add(tp3.mul(ta));
					// TODO tb, pb

					final double tb = (wMin - tp1.getW()) / (tp3.getW() - tp1.getW());
					final Point3D pb = tp1.mul(1 - tb).add(tp3.mul(tb));

					dehomogTriangle(tp1, tp2, pa);
					dehomogTriangle(tp1, pa, pb);
					return;
				}
				// TODO p1 >= wmin
				if (tp1.getW() >= wMin) {// pokud p3 zaporne a p2 zaporne
					return;
				}
			}

			private void dehomogTriangle(final Point3D tp1, final Point3D tp2, final Point3D tp3) {
				final Optional<Vec3D> dp1 = tp1.dehomog();
				final Optional<Vec3D> dp2 = tp2.dehomog();
				final Optional<Vec3D> dp3 = tp3.dehomog();
				dp1.ifPresent((final Vec3D v1) -> {
					dp2.ifPresent(v2 -> {
						dp3.ifPresent(v3 -> {

							drawTriangle(v1, v2, v3);
						});
					});
				});

			}

			private void drawTriangle(Vec3D v1, Vec3D v2, Vec3D v3) {
				final double x1 = (v1.getX() + 1) * 0.5 * (width - 1);
				final double y1 = (-v1.getY() + 1) * 0.5 * (height - 1);
				final double x2 = (v2.getX() + 1) * 0.5 * (width - 1);
				final double y2 = (-v2.getY() + 1) * 0.5 * (height - 1);
				final double x3 = (v3.getX() + 1) * 0.5 * (width - 1);
				final double y3 = (-v3.getY() + 1) * 0.5 * (height - 1);

				triangler.draw(x1, y1, v1.getZ(), x2, y2, v2.getZ(), x3, y3, v3.getZ(), triangleColor);

			}

			private void transformLine(final Point3D p1, final Point3D p2) {
				final Point3D tp1 = p1.mul(mat);
				final Point3D tp2 = p2.mul(mat);
				if (tp1.getW() <= 0 || tp2.getW() <= 0)// orezani usecky jakmile
														// vrchol tak cela
					return;

				final Optional<Vec3D> dp1 = tp1.dehomog();
				final Optional<Vec3D> dp2 = tp2.dehomog();
				dp1.ifPresent((final Vec3D v1) -> {
					dp2.ifPresent(v2 -> {
						drawLine(v1, v2);
					});
				});
			}

			private void drawLine(final Vec3D p1, final Vec3D p2) {// viewportova
																	// transformace
																	// (z NDC do
																	// okna):
				final double x1 = (p1.getX() + 1) * 0.5 * (width - 1);
				final double y1 = (-p1.getY() + 1) * 0.5 * (height - 1);
				final double x2 = (p2.getX() + 1) * 0.5 * (width - 1);
				final double y2 = (-p2.getY() + 1) * 0.5 * (height - 1);
				// lineColor = 0xCC3399;
				liner.drawLine((int) x1, (int) y1, (int) x2, (int) y2, lineColor);
			}
		}

		switch (type) {
		case LINE_LIST:
			new Impl().renderLineList();
			break;
		case TRIANGLE_LIST:
			new Impl().renderTriangleList();
			break;
		}
	}

}
