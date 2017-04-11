package uhk.josef.kanvas;

import java.awt.BorderLayout;
import java.awt.Color;
import java.awt.Dimension;
import java.awt.Graphics;
import java.awt.event.ActionEvent;
import java.awt.event.ActionListener;
import java.awt.event.KeyAdapter;
import java.awt.event.KeyEvent;
import java.awt.event.MouseAdapter;
import java.awt.event.MouseEvent;
import java.awt.event.MouseWheelEvent;
import java.awt.event.MouseWheelListener;
import java.awt.image.BufferedImage;
import javax.swing.JButton;
import javax.swing.JFrame;
import javax.swing.JMenuBar;
import javax.swing.JOptionPane;
import javax.swing.JPanel;
import javax.swing.JRadioButton;
import javax.swing.SwingUtilities;
import rasterization.LineRasterizer;
import rasterization.LineRasterizerTrivial;
import rasterization.TriangleRasterizerScan;
import rendering.Renderer;
import rendering.Renderer.PrimitiveType;
import rendering.RendererPoint3D;
import solids.Cube;
import solids.Curve;
import solids.Grid;
import solids.SimplexPoint3D;
import solids.Axes;
import solids.Solid;
import transforms.*;

/**
 * prubezna uloha cislo 1
 * 
 * @author Josef Janda
 * @version 9.4.2017
 */

public class Canvas {

	private final /* @NotNull */ JFrame frame;
	private final /* @NotNull */ JPanel panel;
	private final /* @NotNull */ BufferedImage img;
	private final LineRasterizer liner;
	private final TriangleRasterizerScan triangler;
	private Camera cam;
	private Mat4 toNDC;
	private Mat4 orthoRh;
	private final Renderer<Point3D> renderer;// vykreslovac
	private boolean setLiner = false;
	private final Solid<Point3D> simplex = new SimplexPoint3D(setLiner);// teleso
	private final Solid<Point3D> cube = new Cube(setLiner);// teleso
	private final Solid<Point3D> axes = new Axes();// osy
	private final Solid<Point3D> grid = new Grid();
	int oldX;
	int oldY;
	private boolean simpl = true;
	private boolean mousseBut = false;
	private boolean cubicDraw = false;
	private boolean drawGrid = false;
	private Mat4 model;
	private Mat4 pom;
	int type = -1;
	private Curve cubic = new Curve(type);

	/**
	 * @param width
	 * @param height
	 */
	public Canvas(int width, int height) {
		frame = new JFrame();
		frame.setTitle("UHK FIM PGRF : Canvas");
		frame.setResizable(true);
		frame.setDefaultCloseOperation(JFrame.EXIT_ON_CLOSE);

		/**
		 * Vytvoøení toolbaru
		 */
		panel = new JPanel();
		JMenuBar jMenuBar = new JMenuBar();
		JButton butCube = new JButton("Cube");
		jMenuBar.add(butCube);
		JButton butSimpl = new JButton("Pyramid");
		jMenuBar.add(butSimpl);
		JButton butCubic = new JButton("Curve");
		jMenuBar.add(butCubic);
		JButton butGrid = new JButton("Grid");
		jMenuBar.add(butGrid);
		JButton butHelp = new JButton("Info");
		jMenuBar.add(butHelp);
		JRadioButton ortho = new JRadioButton("OrthoRH");
		jMenuBar.add(ortho);
		ortho.setSelected(false);
		JRadioButton setLinerB = new JRadioButton("setLiner");
		jMenuBar.add(setLinerB);
		setLinerB.setSelected(false);
		panel.setPreferredSize(new Dimension(width, height));
		frame.add(panel);
		frame.pack();
		frame.setVisible(true);
		frame.setLocationRelativeTo(null);
		frame.setFocusable(true);
		frame.add(jMenuBar, BorderLayout.PAGE_START);

		img = new BufferedImage(width, height, BufferedImage.TYPE_INT_RGB);
		liner = new LineRasterizerTrivial(img);
		triangler = new TriangleRasterizerScan(img);
		renderer = new RendererPoint3D(liner, triangler, height, width);// renderer
																		// = new
																		// RendererPoint3D(liner,
																		// triangler,
																		// height,
																		// height);
		cam = new Camera().withPosition(new Vec3D(5, 4, 5)).withAzimuth(Math.PI + Math.atan(4.0 / 5))
				.withZenith(-Math.PI / 4);
		toNDC = new Mat4PerspRH(Math.PI / 3, (double) height / width, 0.01, 1000);
		orthoRh = new Mat4OrthoRH(10, 10, 0.1, 100);

		ortho.addActionListener(new ActionListener() {

			@Override
			public void actionPerformed(ActionEvent e) {
				if (ortho.isSelected()) {

					pom = new Mat4Identity();
					pom = toNDC;
					toNDC = orthoRh;
					if (!simpl && !cubicDraw && !drawGrid) {
						drawCube();
					}
					if (simpl) {
						drawSimpl();
					}
					if (cubicDraw) {
						drawCurve();
					}
					if (drawGrid) {
						drawGrid();
					}
					frame.requestFocus();
				} else {

					toNDC = pom;
					if (!simpl && !cubicDraw && !drawGrid) {
						drawCube();
					}
					if (simpl) {
						drawSimpl();
					}
					if (cubicDraw) {
						drawCurve();
					}
					if (drawGrid) {
						drawGrid();
					}
					frame.requestFocus();
				}
			}
		});

		setLinerB.addActionListener(new ActionListener() {

			@Override
			public void actionPerformed(ActionEvent e) {
				if (setLinerB.isSelected()) {
					setLiner = true;
					if (!simpl && !cubicDraw && !drawGrid) {
						drawCube();
					}
					if (simpl) {
						drawSimpl();
					}
					if (cubicDraw) {
						drawCurve();
					}
					if (drawGrid) {
						drawGrid();
					}
					present();
					frame.requestFocus();

				} else {
					setLiner = false;
					if (!simpl && !cubicDraw && !drawGrid) {
						drawCube();
					}
					if (simpl) {
						drawSimpl();
					}
					if (cubicDraw) {
						drawCurve();
					}
					if (drawGrid) {
						drawGrid();
					}
					present();
					frame.requestFocus();

				}
			}
		});
		model = new Mat4Identity();

		/**
		 * mouselistener
		 * 
		 */

		class MouseHandler extends MouseAdapter {
			@Override
			public void mousePressed(MouseEvent e) {
				oldX = e.getX();
				oldY = e.getY();
				if (e.getButton() == MouseEvent.BUTTON1) {
					mousseBut = true;
				} else {
					mousseBut = false;
				}
			}

			@Override
			public void mouseReleased(MouseEvent e) {
			}
		}
		panel.addMouseListener(new MouseHandler());
		panel.addMouseMotionListener(new MouseAdapter() {
			@Override
			public void mouseDragged(MouseEvent a) {

				if (mousseBut) {// mousse button1
					cam = cam.addAzimuth(Math.PI * (oldX - a.getX()) / 800);
					oldX = a.getX();
					cam = cam.addZenith(Math.PI * (oldY - a.getY()) / 600);
					oldY = a.getY();

					if (!simpl && !cubicDraw && !drawGrid) {
						drawCube();
					}
					if (simpl) {
						drawSimpl();
					}
					if (cubicDraw) {
						drawCurve();
					}
					if (drawGrid) {
						drawGrid();
					}

				} else {
					clear(0x0);

					Mat4RotXYZ rot = new Mat4RotXYZ((Math.PI * (oldX - a.getX()) / 300) / 80,
							(Math.PI * (oldY - a.getY()) / 300) / 80, 0);

					model = model.mul(rot);

					if (!simpl && !cubicDraw && !drawGrid) {
						drawCube();
					}
					if (simpl) {
						drawSimpl();
					}
					if (cubicDraw) {
						drawCurve();
					}
					if (drawGrid) {
						drawGrid();
					}
					present();

				}
			}
		});

		frame.addMouseWheelListener(new MouseWheelListener() {

			@Override
			public void mouseWheelMoved(MouseWheelEvent e) {

				if (e.getWheelRotation() < 0) {
					cam = cam.forward(0.2);
				} else {
					cam = cam.backward(0.2);
				}

				if (!simpl && !cubicDraw && !drawGrid) {
					drawCube();
				}
				if (simpl) {
					drawSimpl();
				}
				if (cubicDraw) {
					drawCurve();
				}
				if (drawGrid) {
					drawGrid();
				}
				present();

			}
		});

		/**
		 * create button listeners
		 */

		butCube.addMouseListener(new MouseAdapter() {

			@Override
			public void mouseClicked(MouseEvent e) {
				drawCube();
				frame.requestFocus();
			}

		});
		butSimpl.addMouseListener(new MouseAdapter() {

			@Override
			public void mouseClicked(MouseEvent e) {
				simpl = true;
				drawSimpl();
				frame.requestFocus();
			}
		});

		butGrid.addMouseListener(new MouseAdapter() {

			@Override
			public void mouseClicked(MouseEvent e) {
				drawGrid();
				frame.requestFocus();
			}

		});

		butCubic.setToolTipText("Kliknutim zmenite typ krivky");
		butCubic.addMouseListener(new MouseAdapter() {

			@Override
			public void mouseClicked(MouseEvent e) {
				frame.requestFocus();

				if (type != 2) {
					type += 1;
				} else {
					type = 0;
				}
				cubic = new Curve(type);
				drawCurve();
			}
		});

		butHelp.addMouseListener(new MouseAdapter() {

			@Override
			public void mouseClicked(MouseEvent e) {
				frame.requestFocus();
				JOptionPane.showMessageDialog(frame,
						"W,S,A,D pro ovládaní objektu \n" + "Left, Right => pohyb vlevo(osa x,y), vpravo \n"
								+ "Up, Down => pribliz, oddalit(osa z)\n" + "Mouse BTN1+dragged => otáèení kamerou\n"
								+ "Mouse BTN3+dragged => otáèení objekty\n" + "Mouse Wheeled => zooming\n"
								+ "Curve button reaguje na tri kliknuti\n"
								+ "pri prvnim Bezier, druhy Coons a treti Ferguson \n\n"+ "Josef Janda, kai3, 9.4.2017\n",
						"Help", JOptionPane.PLAIN_MESSAGE);
			}
		});
		/**
		 * creating keylisteners
		 */
		frame.addKeyListener(new KeyAdapter() {

			@Override
			public void keyPressed(KeyEvent k) {

				int keyCode = k.getKeyCode();
				if (keyCode == KeyEvent.VK_UP) {
					cam = cam.forward(0.2);

				}
				if (keyCode == KeyEvent.VK_DOWN) {
					cam = cam.backward(0.2);
				}
				if (keyCode == KeyEvent.VK_LEFT) {
					cam = cam.left(0.2);
				}
				if (keyCode == KeyEvent.VK_RIGHT) {
					cam = cam.right(0.2);
				}
				if (keyCode == KeyEvent.VK_W) {
					cam = cam.up(0.2);
				}
				if (keyCode == KeyEvent.VK_S) {
					cam = cam.down(0.2);
				}
				if (keyCode == KeyEvent.VK_A) {
					cam = cam.left(0.2);
				}
				if (keyCode == KeyEvent.VK_D) {
					cam = cam.right(0.2);
				}

				if (!simpl && !cubicDraw && !drawGrid) {
					drawCube();
				}
				if (simpl) {
					drawSimpl();
				}
				if (cubicDraw) {
					drawCurve();
				}
				if (drawGrid) {
					drawGrid();
				}
			}

		});
	}

	/**
	 * drawing objects
	 */
	protected void drawCube() {
		clear(0x0);

		Mat4 mat = model // modelovaci transformace
				.mul(cam.getViewMatrix()) // pohledova transformace
				.mul(toNDC);// transformace objemu viditelnosti
		if (!setLiner) {

			renderer.render(cube.getVertices(), cube.getIndices(), 0, cube.getIndices().size(),
					PrimitiveType.TRIANGLE_LIST, mat, cube.colors());

			mat = new Mat4Transl(-0.1, -0.1, 0.5).mul(mat);
			renderer.render(simplex.getVertices(), simplex.getIndices(), 0, simplex.getIndices().size(),
					PrimitiveType.TRIANGLE_LIST, mat, simplex.colors());
		} else {

			renderer.render(cube.getVertices(), cube.getIndices(), 0, cube.getIndices().size(), PrimitiveType.LINE_LIST,
					mat, cube.colors());
		}
		simpl = false;
		cubicDraw = false;
		drawGrid = false;
		drawAxes();
		present();
	}

	protected void drawSimpl() {
		clear(0x0);
		Mat4 mat = new Mat4Identity().mul(model).mul(cam.getViewMatrix()).mul(toNDC);
		if (!setLiner) {
			renderer.render(simplex.getVertices(), simplex.getIndices(), 0, simplex.getIndices().size(),
					PrimitiveType.TRIANGLE_LIST, mat, simplex.colors());
		} else {
			renderer.render(simplex.getVertices(), simplex.getIndices(), 0, simplex.getIndices().size(),
					PrimitiveType.LINE_LIST, mat, simplex.colors());
		}
		cubicDraw = false;
		drawGrid = false;
		simpl = true;
		drawAxes();
		present();

	}

	private void drawGrid() {
		clear(0x0);
		Mat4 mat = new Mat4Identity().mul(model).mul(cam.getViewMatrix()).mul(toNDC);

		if (!setLiner) {
			renderer.render(grid.getVertices(), grid.getIndices(), 0, grid.getIndices().size(),
					PrimitiveType.TRIANGLE_LIST, mat, grid.colors());
		} else {
			renderer.render(grid.getVertices(), grid.getIndices(), 0, grid.getIndices().size(), PrimitiveType.LINE_LIST,
					mat, grid.colors());
		}
		drawGrid = true;
		cubicDraw = false;
		simpl = false;
		drawAxes();
		present();
	}

	protected void drawCurve() {
		clear(0x0);
		Mat4 mat = new Mat4Identity().mul(model).mul(cam.getViewMatrix()).mul(toNDC);

		renderer.render(cubic.getVertices(), cubic.getIndices(), 0, cubic.getIndices().size(), PrimitiveType.LINE_LIST,
				mat, cubic.colors());
		simpl = false;
		drawGrid = false;
		cubicDraw = true;
		drawAxes();
		present();

	}

	protected void drawAxes() {

		Camera cam1 = new Camera().withPosition(new Vec3D(5, 4, 5)).withAzimuth(Math.PI + Math.atan(4.0 / 5))
				.withZenith(-Math.PI / 4);
		Mat4 mat = new Mat4Identity() // modelovaci transformace.mul(model)
				.mul(model).mul(cam1.getViewMatrix()) // pohledova transformace
				.mul(toNDC);// transformace objemu viditelnosti
		renderer.render(axes.getVertices(), axes.getIndices(), 0, axes.getIndices().size(), PrimitiveType.LINE_LIST,
				mat, axes.colors());// LINE_LIST TRIANGLE
	}

	private void clear(int color) {
		Graphics gr = img.getGraphics();
		gr.setColor(new Color(color));
		gr.fillRect(0, 0, img.getWidth(), img.getHeight());
		drawAxes();
		triangler.clear(color, 1.0);
		//cube.colors().clear();
		
	}

	public void present() {
		if (panel.getGraphics() != null)
			panel.getGraphics().drawImage(img, 0, 0, null);
	}

	public void draw() {
		clear(0x2f2f2f);
		img.setRGB(10, 10, 0xffff00);
		drawAxes();

	}

	public void start() {
		draw();
		present();
		
	}

	public static void main(String[] args) {
		Canvas canvas = new Canvas(1024, 780);
		SwingUtilities.invokeLater(() -> {
			SwingUtilities.invokeLater(() -> {
				SwingUtilities.invokeLater(() -> {
					SwingUtilities.invokeLater(() -> {
						canvas.start();
					});
				});
			});
		});
	}

}