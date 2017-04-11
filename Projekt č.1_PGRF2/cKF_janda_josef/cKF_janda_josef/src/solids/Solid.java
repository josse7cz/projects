package solids;

import java.util.List;

public interface Solid<Vertex> {
	List<Vertex> getVertices();
	List<Integer> getIndices();
	List<Integer> colors();
}
