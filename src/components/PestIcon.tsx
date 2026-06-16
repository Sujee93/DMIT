import {
  TermiteIcon,
  CockroachIcon,
  SpiderIcon,
  RodentIcon,
  AntIcon,
  MosquitoIcon,
  FleaIcon,
  CarpetIcon,
} from "./icons";
import type { SVGProps } from "react";

const map: Record<string, (p: SVGProps<SVGSVGElement>) => React.ReactElement> = {
  termite: TermiteIcon,
  cockroach: CockroachIcon,
  spider: SpiderIcon,
  rodent: RodentIcon,
  ant: AntIcon,
  mosquito: MosquitoIcon,
  flea: FleaIcon,
  carpet: CarpetIcon,
};

export default function PestIcon({
  name,
  ...props
}: { name: string } & SVGProps<SVGSVGElement>) {
  const Cmp = map[name] ?? AntIcon;
  return <Cmp {...props} />;
}
