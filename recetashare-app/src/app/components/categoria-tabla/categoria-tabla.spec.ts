import { ComponentFixture, TestBed } from '@angular/core/testing';

import { CategoriaTablaComponent } from './categoria-tabla';

describe('CategoriaTablaComponent', () => {
  let component: CategoriaTablaComponent;
  let fixture: ComponentFixture<CategoriaTablaComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [CategoriaTablaComponent]
    })
    .compileComponents();

    fixture = TestBed.createComponent(CategoriaTablaComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
