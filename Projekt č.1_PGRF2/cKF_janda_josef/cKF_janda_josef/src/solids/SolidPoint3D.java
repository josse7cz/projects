package solids;

import java.util.ArrayList;
import java.util.List;

import transforms.Point3D;

public abstract class SolidPoint3D implements Solid<Point3D> {
	protected List<Point3D> vertices = new ArrayList<>();//final
	protected List<Integer> indices = new ArrayList<>();//final
	protected List<Integer> colors = new ArrayList<>();//final
	@Override
	public List<Point3D> getVertices() {
		return vertices;
	}
	@Override
	public List<Integer> getIndices() {
		return indices;
	}
	public List<Integer> getColors() {
		return colors;
	}

	
	
}
