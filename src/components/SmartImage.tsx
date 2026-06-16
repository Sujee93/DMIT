"use client";

import { useState } from "react";
import { LeafIcon } from "./icons";

/**
 * Renders a photo with a graceful, on-brand gradient fallback if the image
 * can't load (e.g. offline preview, or before the client swaps in real photos).
 */
export default function SmartImage({
  src,
  alt,
  className = "",
  imgClassName = "",
}: {
  src: string;
  alt: string;
  className?: string;
  imgClassName?: string;
}) {
  const [failed, setFailed] = useState(false);

  return (
    <div className={`relative overflow-hidden bg-forest-800 ${className}`}>
      {!failed ? (
        // eslint-disable-next-line @next/next/no-img-element
        <img
          src={src}
          alt={alt}
          loading="lazy"
          onError={() => setFailed(true)}
          className={`h-full w-full object-cover ${imgClassName}`}
        />
      ) : (
        <div
          aria-label={alt}
          role="img"
          className="flex h-full w-full items-center justify-center bg-gradient-to-br from-brand-600 via-forest-700 to-forest-900"
        >
          <LeafIcon className="h-1/3 w-1/3 max-h-24 max-w-24 text-white/15" />
        </div>
      )}
    </div>
  );
}
