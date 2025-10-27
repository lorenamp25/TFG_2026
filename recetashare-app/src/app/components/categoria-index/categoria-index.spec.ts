import { ComponentFixture, TestBed } from '@angular/core/testing';

import { CategoriaIndex } from './categoria-index';

describe('CategoriaIndex', () => {
  let component: CategoriaIndex;
  let fixture: ComponentFixture<CategoriaIndex>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [CategoriaIndex]
    })
    .compileComponents();

    fixture = TestBed.createComponent(CategoriaIndex);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
