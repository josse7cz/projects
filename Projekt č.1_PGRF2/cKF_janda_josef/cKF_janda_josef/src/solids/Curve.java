package solids;

import java.util.List;

import transforms.Cubic;
import transforms.Mat4;
import transforms.Point3D;

public class Curve extends SolidPoint3D {

	private Cubic cubic;

	public static final Point3D p1 = new Point3D(0, 0, 0);
	public static final Point3D p2 = new Point3D(1, 0, 0);
	public static final Point3D p3 = new Point3D(1, 0, 1);
	public static final Point3D p4 = new Point3D(1, 1, 1);

	public Curve(int type) {
		this(null, null, null, null, null, type);
	}

	public Curve(Mat4 cubic, final Point3D point1, final Point3D point2, final Point3D point3, final Point3D point4,
			int type) {
		this(800, cubic, point1, point2, point3, point4, type);
	}

	public Curve(int points, Mat4 cubic, Point3D point1, Point3D point2, Point3D point3, Point3D point4, int type) {
		if (points < 0)
			return;
		if (cubic == null)
			cubic = Cubic.BEZIER;
		if (type == 0)
			cubic = Cubic.BEZIER;
		if (type == 1)
			cubic = Cubic.COONS;
		if (type == 2)
			cubic = Cubic.FERGUSON;
		point1 = p1;
		point2 = p2;
		point3 = p3;
		point4 = p4;
		colors.add(0x99FF33);
		colors.add(0xCC3399);
		

		this.cubic = new Cubic(cubic, point1, point2, point3, point4);
		for (int i = 0; i < points; i++) {
			vertices.add(this.cubic.compute((double) i / points));
			if (i != 0) {
				indices.add(i - 1);
				indices.add(i);
			}
		}
	}

	@Override
	public List<Integer> colors() {
		// TODO Auto-generated method stub
		return colors;
	}

}
